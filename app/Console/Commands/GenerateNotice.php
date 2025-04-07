<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates rental notices by exporting PDF template form (requires pdfcpu)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Exporting PDF form template...');

        // Create timestamp for the filename
        $timestamp = date('Y-m-d_H-i-s');
        $destinationFile = storage_path("app/notices/10-day-notice-{$timestamp}.pdf");

        // Ensure the directory exists
        if (!file_exists(dirname($destinationFile))) {
            mkdir(dirname($destinationFile), 0755, true);
        }

        // Copy the template file to storage with timestamp name
        if (!copy(base_path('templates/10-day-notice-template.pdf'), $destinationFile)) {
            $this->error('Failed to copy template file to storage');
            return 1;
        }

        $this->info("Template copied to: {$destinationFile}");

        // TODO: Add an owner password.
        $command = "pdfcpu form fill {$destinationFile} templates/10-day-notice-template.json";
        $output = [];
        $returnVar = 0;

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info('PDF form filled successfully!');
            $this->line(implode("\n", $output));
        } else {
            $this->error('Failed to fill PDF form');
            $this->line(implode("\n", $output));
        }

        return $returnVar;
    }
}
