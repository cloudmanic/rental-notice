<?php

namespace App\Console\Commands;

use App\Jobs\PrintNoticePackageJob;
use App\Models\Notice;
use Illuminate\Console\Command;

class PrintNoticePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notice:print {notice : The ID of the notice to print}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and print a complete notice package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $noticeId = $this->argument('notice');

        // Find the notice
        $notice = Notice::find($noticeId);

        if (! $notice) {
            $this->error("Notice with ID {$noticeId} not found.");

            return 1;
        }

        // Check if notice has required status
        if (! in_array($notice->status, ['paid', 'service_pending', 'served'])) {
            $this->warn("Notice {$noticeId} has status '{$notice->status}'. Only notices with status 'paid', 'service_pending', or 'served' can be printed.");

            if (! $this->confirm('Do you want to continue anyway?')) {
                return 0;
            }
        }

        $this->info("Dispatching print job for notice {$noticeId}...");

        // Dispatch the job
        PrintNoticePackageJob::dispatch($notice);

        $this->info('Print job dispatched successfully. Check the queue worker logs for progress.');

        return 0;
    }
}
