<?php

namespace App\Console\Commands;

use App\Models\ColdOutReachList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateColdOutreachDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-cold-outreach-domains {--limit=10 : Limit the number of records to process} {--debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds domains for cold outreach list entries using the first valid Google Search result (skipping specified domains).';

    /**
     * Domains to skip during the search.
     *
     * @var array
     */
    protected $skipDomains = [
        'facebook.com',
        'yelp.com',
        'linkedin.com',
        'bbb.org',
        'twitter.com',
        'instagram.com',
        'youtube.com',
        'google.com',
        'microsoft.com',
        'bozemanrental.com',
        'accuratecg.com',
        'llr.sc.gov',
        'support.microsoft.com',
        'pubmed.ncbi.nlm.nih.gov',
        'aeon.org',
        'toolkit.climate.gov',
        'climate.gov',
        'science.gsfc.nasa.gov',
        'nasa.gov',
        'amoriss.com',
        'a1propertyman.com',
        'propertymanagerdirectory.com',
        'accuratecg.com',
        'areswms.com',
        'aapropertymanagement.com',
        'provequity.com',
        'americanrentalsllc.com',
        'amoriss.com',
        'portland-apartment-living.com',
        'aegispg.com',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $debug = $this->option('debug');
        $processedCount = 0;
        $updatedCount = 0;

        $this->info("Starting domain update process. Processing up to {$limit} records.");

        ColdOutReachList::whereNull('domain')
            ->orWhere('domain', '')
            ->chunkById(100, function ($records) use (&$processedCount, &$updatedCount, $debug, $limit) {
                foreach ($records as $record) {
                    if ($processedCount >= $limit) {
                        $this->info("Reached processing limit of {$limit}.");
                        return false; // Stop processing further chunks
                    }

                    $processedCount++;
                    $this->line("\nProcessing ID: {$record->id}, Company: {$record->company_name}, State: {$record->state} ({$processedCount}/{$limit})");

                    if (empty($record->company_name) || empty($record->state)) {
                        $this->warn("Skipping ID: {$record->id} due to missing company name or state.");
                        continue;
                    }

                    // Prepare company name for search: remove LLC (case-insensitive) and commas
                    $cleanCompanyName = str_ireplace([' LLC', ', LLC', ' LLC,', 'LLC'], '', $record->company_name);
                    $cleanCompanyName = str_replace(',', '', $cleanCompanyName);
                    $cleanCompanyName = trim($cleanCompanyName); // Remove any leading/trailing whitespace

                    $searchTerm = $cleanCompanyName . ' ' . $record->state;
                    $this->line("  Search Term: '{$searchTerm}'"); // Show the modified search term if debugging

                    $url = $this->searchGoogle($searchTerm, $debug);

                    if ($url) {
                        $this->info("Found valid URL: {$url}");
                        $domain = $this->extractDomain($url);
                        if ($domain) {
                            $record->domain = $domain;
                            $record->save();
                            $updatedCount++;
                            $this->info("Successfully updated domain to: {$domain}");
                        } else {
                            $this->warn("Could not extract domain from URL: {$url}");
                        }
                    } else {
                        $this->warn("No valid URL found or first result skipped for search term: '{$searchTerm}'");
                    }
                }

                if ($processedCount >= $limit) {
                    return false; // Stop processing further chunks
                }

                return true; // Continue processing next chunk if limit not reached
            });

        $this->info("\nFinished processing.");
        $this->info("Total records checked: {$processedCount}");
        $this->info("Domains updated: {$updatedCount}");

        return 0;
    }

    /**
     * Search Google using the Custom Search API for the first valid result.
     * Filters out specified domains.
     *
     * @return string|null The first valid URL or null
     */
    private function searchGoogle(string $searchTerm, bool $debug): ?string
    {
        try {
            $apiKey = config('services.google.search_api_key');
            $searchEngineId = config('services.google.search_engine_id');

            if (!$apiKey || !$searchEngineId) {
                $this->error('Google Search API Key or Search Engine ID is not configured in config/services.php or .env');
                Log::error('Google Search API Key or Search Engine ID is not configured.');
                return null;
            }

            $response = Http::timeout(15)->get('https://www.googleapis.com/customsearch/v1', [
                'key' => $apiKey,
                'cx' => $searchEngineId,
                'q' => $searchTerm,
                'num' => 1 // Only request 1 result
            ]);

            if ($response->successful()) {
                $results = $response->json();
                if (isset($results['items']) && count($results['items']) > 0) {
                    $firstResult = $results['items'][0];
                    if (isset($firstResult['link'])) {
                        $url = $firstResult['link'];
                        $domain = $this->extractDomain($url);
                        if ($domain && !in_array($domain, $this->skipDomains)) {
                            return $url; // Return the first valid URL
                        } elseif ($debug && $domain) {
                            $this->line("  Skipping domain from first result: {$domain} ({$url})");
                            return null;
                        } elseif ($debug && !$domain) {
                            $this->line("  Could not extract domain from first result URL: {$url}");
                            return null;
                        }
                    } else {
                        if ($debug) $this->line('First search result item has no link.');
                        return null;
                    }
                } else {
                    if ($debug) $this->line('No search results found in API response.');
                    return null;
                }
            } else {
                $this->error('Error from Google Search API: ' . $response->status());
                Log::error('Google Search API Error: ' . $response->body());
                if ($debug) {
                    $this->line('Response Body: ' . $response->body());
                }
                return null;
            }
        } catch (\Exception $e) {
            $this->error('An error occurred during Google Search: ' . $e->getMessage());
            Log::error('Google Search Exception: ' . $e->getMessage());
            return null;
        }

        return null;
    }

    /**
     * Extract the domain name from a URL.
     */
    private function extractDomain(string $url): ?string
    {
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['host'])) {
            $host = Str::lower($parsedUrl['host']);
            return Str::startsWith($host, 'www.') ? Str::substr($host, 4) : $host;
        }

        return null;
    }
}
