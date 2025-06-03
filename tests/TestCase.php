<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Fake the queue to prevent any jobs from being processed during tests.
        // This is crucial because notifications that implement ShouldQueue
        // (like UserRegistered) would otherwise be sent if a queue worker
        // is running in the background, even with Notification::fake().
        // With Queue::fake(), notification jobs are captured but never executed.
        Queue::fake();
    }
}
