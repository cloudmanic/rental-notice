<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;

class CheckActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check activities to verify event field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activities = Activity::select('id', 'agent_id', 'tenant_id', 'notice_id', 'event', 'description')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        $this->info('Recent Activities:');
        
        foreach ($activities as $activity) {
            $this->line("ID: {$activity->id}, Event: {$activity->event}, Description: {$activity->description}");
        }
        
        // Check event types distribution
        $eventCounts = Activity::selectRaw('event, count(*) as count')
            ->groupBy('event')
            ->get();
            
        $this->info("\nEvent Type Distribution:");
        
        foreach ($eventCounts as $eventCount) {
            $this->line("{$eventCount->event}: {$eventCount->count}");
        }
    }
}
