<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GoogleSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:google-search 
                            {term : The search term to look for} 
                            {--oregon : Filter results for Oregon companies}
                            {--debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search Google and return the URL of the first result';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchTerm = $this->argument('term');
        $filterForOregon = $this->option('oregon');

        // Append "Oregon" to the search term if the option is specified
        if ($filterForOregon) {
            $searchTerm .= ' Oregon company';
        }

        $this->info("Searching for: $searchTerm");

        try {
            $apiKey = config('services.google.search_api_key');
            $searchEngineId = config('services.google.search_engine_id');

            // Generate the API request URL for debugging
            $requestUrl = 'https://www.googleapis.com/customsearch/v1?key=' . substr($apiKey, 0, 3) . '...' .
                '&cx=' . substr($searchEngineId, 0, 3) . '...' .
                '&q=' . urlencode($searchTerm);

            // Make the API request
            $response = Http::get('https://www.googleapis.com/customsearch/v1', [
                'key' => $apiKey,
                'cx' => $searchEngineId,
                'q' => $searchTerm,
            ]);

            if ($response->successful()) {
                $results = $response->json();

                if (isset($results['items']) && count($results['items']) > 0) {
                    $firstResult = $results['items'][0];
                    $url = $firstResult['link'];
                    $title = $firstResult['title'];

                    $this->info("First result: $title");
                    $this->info("URL: $url");

                    return $url;
                } else {
                    $this->warn('No search results found.');
                    return null;
                }
            } else {
                $responseBody = $response->body();
                $this->error('Error from Google Search API: ' . $responseBody);

                if ($debug) {
                    // More detailed error analysis
                    $responseData = json_decode($responseBody, true);
                    if (isset($responseData['error'])) {
                        $this->line('Error details:');
                        $this->line('- Code: ' . ($responseData['error']['code'] ?? 'Unknown'));
                        $this->line('- Message: ' . ($responseData['error']['message'] ?? 'Unknown'));

                        if (isset($responseData['error']['errors']) && is_array($responseData['error']['errors'])) {
                            foreach ($responseData['error']['errors'] as $index => $error) {
                                $this->line("- Error $index domain: " . ($error['domain'] ?? 'Unknown'));
                                $this->line("  Reason: " . ($error['reason'] ?? 'Unknown'));
                            }
                        }
                    }

                    // Provide suggestions based on common errors
                    if (str_contains($responseBody, 'invalid API key')) {
                        $this->line("\nSuggestion: Your API key appears to be invalid. Please check it in your .env file.");
                    } else if (str_contains($responseBody, 'API key not valid')) {
                        $this->line("\nSuggestion: Your API key may not be valid or may not have the Custom Search API enabled.");
                        $this->line("Visit https://console.cloud.google.com/ to enable the Custom Search API for your project.");
                    } else if (str_contains($responseBody, 'invalid argument')) {
                        $this->line("\nSuggestion: Some parameter is invalid. Make sure both your API key and Search Engine ID are correct.");
                        $this->line("1. Verify GOOGLE_SEARCH_API_KEY in your .env file");
                        $this->line("2. Verify GOOGLE_SEARCH_ENGINE_ID in your .env file");
                        $this->line("3. Try running: php artisan config:cache to refresh your configuration");
                    }
                }

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
