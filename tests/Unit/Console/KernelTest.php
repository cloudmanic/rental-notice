<?php

namespace Tests\Unit\Console;

use App\Console\Kernel;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class KernelTest extends TestCase
{
    /**
     * Test that notice reminders are scheduled at 9 AM PST.
     */
    public function test_notice_reminders_scheduled_at_9am_pst()
    {
        $schedule = app(Schedule::class);
        $kernel = app(Kernel::class);

        // Clear any existing scheduled events
        $reflection = new \ReflectionClass($schedule);
        $eventsProperty = $reflection->getProperty('events');
        $eventsProperty->setAccessible(true);
        $eventsProperty->setValue($schedule, []);

        // Use reflection to call the protected schedule method
        $kernelReflection = new \ReflectionClass($kernel);
        $scheduleMethod = $kernelReflection->getMethod('schedule');
        $scheduleMethod->setAccessible(true);
        $scheduleMethod->invoke($kernel, $schedule);

        // Get the scheduled events
        $events = $schedule->events();

        // Find the notice:send-reminders command
        $reminderEvent = null;
        foreach ($events as $event) {
            if (strpos($event->command, 'notice:send-reminders') !== false) {
                $reminderEvent = $event;
                break;
            }
        }

        $this->assertNotNull($reminderEvent, 'notice:send-reminders command should be scheduled');

        // Check that the timezone is set to America/Los_Angeles (PST)
        $this->assertEquals('America/Los_Angeles', $reminderEvent->timezone);

        // Check that it contains the correct time expression for 9:00 AM daily
        $this->assertEquals('0 9 * * *', $reminderEvent->expression);
    }
}
