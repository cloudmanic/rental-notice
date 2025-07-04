<?php

namespace App\Console\Commands;

use App\Models\RealtorList;
use App\Models\Referrer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportRealtorsToReferrers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:realtors-to-referrers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import realtors from realtor_list table to referrers table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting realtor to referrer import...');

        $planDate = '2024-01-03 00:00:00';
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        // Get all realtors with required data
        $realtors = RealtorList::whereNotNull('first_name')
            ->whereNotNull('last_name')
            ->whereNotNull('email')
            ->chunk(100, function ($realtorChunk) use ($planDate, &$importedCount, &$skippedCount, &$errorCount) {
                foreach ($realtorChunk as $realtor) {
                    try {
                        // Check if referrer already exists with this email
                        if (Referrer::where('email', $realtor->email)->exists()) {
                            $this->warn("Skipping {$realtor->email} - already exists as referrer");
                            $skippedCount++;

                            continue;
                        }

                        // Generate unique slug using custom logic
                        $slug = $this->generateCustomUniqueSlug($realtor->first_name, $realtor->last_name);

                        // Create referrer
                        Referrer::create([
                            'first_name' => $realtor->first_name,
                            'last_name' => $realtor->last_name,
                            'email' => $realtor->email,
                            'slug' => $slug,
                            'plan_date' => $planDate,
                            'is_active' => true,
                        ]);

                        $this->line("Imported: {$realtor->first_name} {$realtor->last_name} ({$realtor->email}) -> {$slug}");
                        $importedCount++;
                    } catch (\Exception $e) {
                        $this->error("Error importing {$realtor->email}: {$e->getMessage()}");
                        $errorCount++;
                    }
                }
            });

        $this->newLine();
        $this->info('Import completed!');
        $this->table(['Metric', 'Count'], [
            ['Imported', $importedCount],
            ['Skipped (already exists)', $skippedCount],
            ['Errors', $errorCount],
        ]);

        return Command::SUCCESS;
    }

    /**
     * Generate a unique slug using custom logic.
     * Try: firstname-lastname, lastname-firstname, lastname, firstname, firstname-lastname-{random}
     */
    private function generateCustomUniqueSlug(string $firstName, string $lastName): string
    {
        $firstNameSlug = Str::slug($firstName);
        $lastNameSlug = Str::slug($lastName);

        // Strategy 1: firstname-lastname
        $slug = $firstNameSlug.'-'.$lastNameSlug;
        if (! Referrer::where('slug', $slug)->exists()) {
            return $slug;
        }

        // Strategy 2: lastname-firstname
        $slug = $lastNameSlug.'-'.$firstNameSlug;
        if (! Referrer::where('slug', $slug)->exists()) {
            return $slug;
        }

        // Strategy 3: lastname only
        $slug = $lastNameSlug;
        if (! Referrer::where('slug', $slug)->exists()) {
            return $slug;
        }

        // Strategy 4: firstname only
        $slug = $firstNameSlug;
        if (! Referrer::where('slug', $slug)->exists()) {
            return $slug;
        }

        // Strategy 5: firstname-lastname-{random number}
        $baseSlug = $firstNameSlug.'-'.$lastNameSlug;
        do {
            $randomNumber = random_int(1000, 9999);
            $slug = $baseSlug.'-'.$randomNumber;
        } while (Referrer::where('slug', $slug)->exists());

        return $slug;
    }
}
