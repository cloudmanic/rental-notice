<?php

namespace App\Jobs;

use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class PrintNoticePackageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The notice instance.
     *
     * @var Notice
     */
    public $notice;

    /**
     * Create a new job instance.
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    /**
     * Execute the job.
     */
    public function handle(NoticeService $noticeService): void
    {
        // Get SSH connection details
        $host = $this->getHost();
        $username = $this->getUsername();

        // Check if required environment variables are set
        if (empty($host) || empty($username)) {
            Log::info('Print job skipped - print server configuration not set', [
                'notice_id' => $this->notice->id,
                'host_set' => ! empty($host),
                'username_set' => ! empty($username),
            ]);

            return;
        }

        try {
            Log::info('Starting print job for notice', ['notice_id' => $this->notice->id]);

            // Generate the complete print package
            $storagePath = $noticeService->generateCompletePrintPackage($this->notice);
            $localPath = Storage::disk('local')->path($storagePath);
            Log::info('Generated print package', ['storage_path' => $storagePath, 'local_path' => $localPath]);

            // Build unique filename for the print server
            $remoteFilename = 'notice_'.$this->notice->id.'_'.time().'.pdf';
            $remotePath = '/tmp/'.$remoteFilename;

            // Get remaining SSH connection details
            $port = $this->getPort();
            $printer = $this->getPrinter();

            // SCP the file to the print server
            $this->scpFileToServer($localPath, $remotePath, $host, $port, $username);

            // Print the file
            $this->printFile($remotePath, $printer, $host, $port, $username);

            // Clean up local file
            if (file_exists($localPath)) {
                unlink($localPath);
            }

            Log::info('Print job completed successfully', ['notice_id' => $this->notice->id]);
        } catch (\Exception $e) {
            Log::error('Print job failed', [
                'notice_id' => $this->notice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * SCP file to the print server.
     */
    protected function scpFileToServer(string $localPath, string $remotePath, string $host, string $port, string $username): void
    {
        $command = [
            'scp',
            '-P',
            $port,
            $localPath,
            "{$username}@{$host}:{$remotePath}",
        ];

        Log::info('Copying file to print server', ['command' => implode(' ', $command)]);

        if (! $this->executeProcess($command)) {
            throw new \RuntimeException('Failed to copy file to print server: '.$this->getLastError());
        }

        Log::info('File copied to print server successfully');
    }

    /**
     * Send print command to the print server.
     *
     * For testing sometimes I add a print range to the lpr command, e.g. `-o page-ranges=2-2`
     */
    protected function printFile(string $remotePath, string $printer, string $host, string $port, string $username): void
    {
        // Now send the print command
        $command = [
            'ssh',
            '-p',
            $port,
            "{$username}@{$host}",
            "lpr -P {$printer} {$remotePath} && rm {$remotePath}",  // Print and remove the file
        ];

        Log::info('Sending print command', ['command' => implode(' ', $command)]);

        if (! $this->executeProcess($command)) {
            throw new \RuntimeException('Failed to print file: '.$this->getLastError());
        }

        Log::info('Print command sent successfully');
    }

    /**
     * Execute a process command.
     * This method is extracted to make the class more testable.
     */
    protected function executeProcess(array $command): bool
    {
        $process = new Process($command);
        $process->setTimeout(120); // 2 minute timeout
        $process->run();

        if (! $process->isSuccessful()) {
            $this->lastProcessError = $process->getErrorOutput();

            return false;
        }

        return true;
    }

    /**
     * Get the last process error.
     */
    protected function getLastError(): string
    {
        return $this->lastProcessError ?? '';
    }

    /**
     * Get the print server host.
     */
    protected function getHost(): string
    {
        return env('PRINT_SERVER_HOST', '');
    }

    /**
     * Get the print server username.
     */
    protected function getUsername(): string
    {
        return env('PRINT_SERVER_USERNAME', '');
    }

    /**
     * Get the print server port.
     */
    protected function getPort(): string
    {
        return env('PRINT_SERVER_PORT', '22');
    }

    /**
     * Get the printer name.
     */
    protected function getPrinter(): string
    {
        return env('PRINT_SERVER_PRINTER', 'Brother_HL_L2405W');
    }

    /**
     * Store the last process error.
     */
    protected string $lastProcessError = '';
}
