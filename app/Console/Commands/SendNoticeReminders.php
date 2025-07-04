<?php

namespace App\Console\Commands;

use App\Mail\NoticeReminder;
use App\Models\Notice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNoticeReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notice:send-reminders {--days=1 : Number of days after creation to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for notices that are pending payment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysOld = (int) $this->option('days');

        // Find notices that are pending payment, older than specified days, and haven't had reminders sent
        $notices = Notice::with(['user', 'tenants', 'noticeType'])
            ->where('status', Notice::STATUS_PENDING_PAYMENT)
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', now()->subDays($daysOld))
            ->get();

        if ($notices->isEmpty()) {
            $this->info('No notices found that need reminder emails.');

            return 0;
        }

        $sentCount = 0;

        foreach ($notices as $notice) {
            try {
                // Send the reminder email
                Mail::to($notice->user->email)->send(new NoticeReminder($notice, $notice->user));

                // Mark reminder as sent
                $notice->update(['reminder_sent_at' => now()]);

                $sentCount++;

                $this->info("Sent reminder for notice #{$notice->id} to {$notice->user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for notice #{$notice->id}: ".$e->getMessage());
            }
        }

        $this->info("Sent {$sentCount} reminder emails out of {$notices->count()} eligible notices.");

        return 0;
    }
}
