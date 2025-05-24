<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SshListFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssh:list-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SSH into the print server and list files using system SSH';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = env('PRINT_SERVER_HOST');
        $port = env('PRINT_SERVER_PORT', 22);
        $username = env('PRINT_SERVER_USERNAME');

        $this->info("Connecting to {$username}@{$host} on port {$port}...");

        // Build the SSH command
        $command = [
            'ssh',
            '-p',
            $port,
            "{$username}@{$host}",
            'lpr -P Brother_HL_L2405W ~/Downloads/10-day-notice-template.pdf',  // The command to run on the remote server
        ];

        // Create a new process
        $process = new Process($command);
        $process->setTimeout(60); // Set a timeout of 60 seconds

        // Run the process
        try {
            $this->info('Running: '.implode(' ', $command));

            // Output will be echoed to the console in real-time
            $process->setTty(true);
            $exitCode = $process->run(function ($type, $buffer) {
                // Process is running with TTY so this callback won't be called unless TTY is not available
                if ($type === Process::ERR) {
                    $this->error($buffer);
                } else {
                    $this->line($buffer);
                }
            });

            // If TTY is not available, we'll fall back to showing the output after the process completes
            if (! $process->isTtySupported()) {
                $this->info('Output:');
                $this->line($process->getOutput());

                if ($process->getErrorOutput()) {
                    $this->error('Error:');
                    $this->error($process->getErrorOutput());
                }
            }

            return $exitCode;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }
    }
}
