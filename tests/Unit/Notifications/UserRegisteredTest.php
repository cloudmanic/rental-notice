<?php

namespace Tests\Unit\Notifications;

use App\Models\Referrer;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Slack\SlackMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRegisteredTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_includes_referrer_information_in_slack_notification()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $referrer = Referrer::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
        ]);

        $notification = new UserRegistered($user, 'Test Company', $referrer);

        // Get the Slack representation
        $slackMessage = $notification->toSlack($user);

        // Check that it's a SlackMessage
        $this->assertInstanceOf(SlackMessage::class, $slackMessage);

        // Get the JSON data from the notification
        $arrayData = $notification->toArray($user);

        // Verify referrer information is included
        $this->assertEquals($referrer->id, $arrayData['referrer_id']);
        $this->assertEquals('Jane Smith', $arrayData['referrer_name']);
        $this->assertEquals('jane@example.com', $arrayData['referrer_email']);
        $this->assertArrayHasKey('referral_discount', $arrayData);
    }

    #[Test]
    public function it_works_without_referrer_information()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $notification = new UserRegistered($user, 'Test Company');

        // Get the Slack representation
        $slackMessage = $notification->toSlack($user);

        // Check that it's a SlackMessage
        $this->assertInstanceOf(SlackMessage::class, $slackMessage);

        // Get the JSON data from the notification
        $arrayData = $notification->toArray($user);

        // Verify referrer information is NOT included
        $this->assertArrayNotHasKey('referrer_id', $arrayData);
        $this->assertArrayNotHasKey('referrer_name', $arrayData);
        $this->assertArrayNotHasKey('referrer_email', $arrayData);
        $this->assertArrayNotHasKey('referral_discount', $arrayData);
    }

    #[Test]
    public function it_changes_slack_header_for_referral_registrations()
    {
        $user = User::factory()->create();
        $referrer = Referrer::factory()->create();

        // With referrer
        $notificationWithReferrer = new UserRegistered($user, 'Test Company', $referrer);
        $slackMessageWithReferrer = $notificationWithReferrer->toSlack($user);
        
        // The template should contain "New Referral Registration"
        $this->assertInstanceOf(SlackMessage::class, $slackMessageWithReferrer);

        // Without referrer
        $notificationWithoutReferrer = new UserRegistered($user, 'Test Company');
        $slackMessageWithoutReferrer = $notificationWithoutReferrer->toSlack($user);
        
        // The template should contain "New User Registration"
        $this->assertInstanceOf(SlackMessage::class, $slackMessageWithoutReferrer);
    }
}