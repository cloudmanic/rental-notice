<?php

namespace Tests\Unit\Services;

use App\Services\DateService;
use Carbon\Carbon;
use Tests\TestCase;

class DateServiceTest extends TestCase
{
    protected DateService $dateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dateService = new DateService;
    }

    /**
     * Test that getNextMailingDate returns today if before cutoff time
     */
    public function test_get_next_mailing_date_before_cutoff()
    {
        // Test at 10 AM PST (before 1 PM cutoff)
        $testDate = Carbon::parse('2025-01-24 10:00:00', 'America/Los_Angeles');
        $mailingDate = $this->dateService->getNextMailingDate($testDate);

        $this->assertEquals('2025-01-24', $mailingDate->format('Y-m-d'));
    }

    /**
     * Test that getNextMailingDate returns next day if after cutoff time
     */
    public function test_get_next_mailing_date_after_cutoff()
    {
        // Test at 2 PM PST (after 1 PM cutoff)
        $testDate = Carbon::parse('2025-01-24 14:00:00', 'America/Los_Angeles');
        $mailingDate = $this->dateService->getNextMailingDate($testDate);

        $this->assertEquals('2025-01-25', $mailingDate->format('Y-m-d'));
    }

    /**
     * Test that Sunday is skipped to Monday
     */
    public function test_sunday_is_skipped_to_monday()
    {
        // Saturday after cutoff should go to Monday
        $testDate = Carbon::parse('2025-01-25 14:00:00', 'America/Los_Angeles'); // Saturday 2 PM
        $mailingDate = $this->dateService->getNextMailingDate($testDate);

        $this->assertEquals('2025-01-27', $mailingDate->format('Y-m-d')); // Monday
        $this->assertTrue($mailingDate->isMonday());
    }

    /**
     * Test that Sunday before cutoff still goes to Monday
     */
    public function test_sunday_before_cutoff_goes_to_monday()
    {
        // Sunday morning should go to Monday
        $testDate = Carbon::parse('2025-01-26 10:00:00', 'America/Los_Angeles'); // Sunday 10 AM
        $mailingDate = $this->dateService->getNextMailingDate($testDate);

        $this->assertEquals('2025-01-27', $mailingDate->format('Y-m-d')); // Monday
    }

    /**
     * Test isPastCutoffTime returns correct boolean
     */
    public function test_is_past_cutoff_time()
    {
        // Before cutoff
        $beforeCutoff = Carbon::parse('2025-01-24 12:00:00', 'America/Los_Angeles');
        $this->assertFalse($this->dateService->isPastCutoffTime($beforeCutoff));

        // After cutoff
        $afterCutoff = Carbon::parse('2025-01-24 14:00:00', 'America/Los_Angeles');
        $this->assertTrue($this->dateService->isPastCutoffTime($afterCutoff));

        // Exactly at cutoff
        $atCutoff = Carbon::parse('2025-01-24 13:00:00', 'America/Los_Angeles');
        $this->assertFalse($this->dateService->isPastCutoffTime($atCutoff));
    }

    /**
     * Test formatMailingDate returns correct format
     */
    public function test_format_mailing_date()
    {
        $date = Carbon::parse('2025-01-24');
        $formatted = $this->dateService->formatMailingDate($date);

        $this->assertEquals('January 24, 2025', $formatted);

        // Test custom format
        $customFormatted = $this->dateService->formatMailingDate($date, 'm/d/Y');
        $this->assertEquals('01/24/2025', $customFormatted);
    }

    /**
     * Test calculateServiceDate for 10-day notice
     */
    public function test_calculate_service_date_for_10_day_notice()
    {
        $mailingDate = Carbon::parse('2025-01-24');
        $serviceDate = $this->dateService->calculateServiceDate($mailingDate, 10);

        // 1 day (skip mailing day) + 10 days (notice period) + 4 days (mailing) = 15 days
        $this->assertEquals('2025-02-08', $serviceDate->format('Y-m-d'));
        $this->assertEquals(15, $mailingDate->diffInDays($serviceDate));
    }

    /**
     * Test calculateServiceDate for 13-day notice
     */
    public function test_calculate_service_date_for_13_day_notice()
    {
        $mailingDate = Carbon::parse('2025-01-24');
        $serviceDate = $this->dateService->calculateServiceDate($mailingDate, 13);

        // 1 day (skip mailing day) + 13 days (notice period) + 4 days (mailing) = 18 days
        $this->assertEquals('2025-02-11', $serviceDate->format('Y-m-d'));
        $this->assertEquals(18, $mailingDate->diffInDays($serviceDate));
    }

    /**
     * Test calculateServiceDate defaults to 10-day notice
     */
    public function test_calculate_service_date_defaults_to_10_day()
    {
        $mailingDate = Carbon::parse('2025-01-24');
        $serviceDate = $this->dateService->calculateServiceDate($mailingDate);

        // Should default to 10-day notice calculation
        $this->assertEquals('2025-02-08', $serviceDate->format('Y-m-d'));
        $this->assertEquals(15, $mailingDate->diffInDays($serviceDate));
    }

    /**
     * Test timezone handling for different timezones
     */
    public function test_timezone_handling()
    {
        // Test from Eastern timezone (3 hours ahead of PST)
        $easternDate = Carbon::parse('2025-01-24 16:00:00', 'America/New_York'); // 4 PM EST = 1 PM PST
        $mailingDate = $this->dateService->getNextMailingDate($easternDate);

        // Should be today since it's exactly 1 PM PST
        $this->assertEquals('2025-01-24', $mailingDate->format('Y-m-d'));

        // One minute later should be next day
        $easternDateLater = Carbon::parse('2025-01-24 16:01:00', 'America/New_York'); // 4:01 PM EST = 1:01 PM PST
        $mailingDateLater = $this->dateService->getNextMailingDate($easternDateLater);

        $this->assertEquals('2025-01-25', $mailingDateLater->format('Y-m-d'));
    }

    /**
     * Test with custom cutoff time configuration
     */
    public function test_custom_cutoff_time()
    {
        // Temporarily change the config
        config(['constants.mailing.cutoff_time' => '15:00']); // 3 PM

        // Test at 2 PM - should be today
        $testDate = Carbon::parse('2025-01-24 14:00:00', 'America/Los_Angeles');
        $mailingDate = $this->dateService->getNextMailingDate($testDate);
        $this->assertEquals('2025-01-24', $mailingDate->format('Y-m-d'));

        // Test at 4 PM - should be tomorrow
        $testDateAfter = Carbon::parse('2025-01-24 16:00:00', 'America/Los_Angeles');
        $mailingDateAfter = $this->dateService->getNextMailingDate($testDateAfter);
        $this->assertEquals('2025-01-25', $mailingDateAfter->format('Y-m-d'));

        // Reset config
        config(['constants.mailing.cutoff_time' => '13:00']);
    }
}
