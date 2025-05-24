<?php

namespace Tests\Unit\Livewire\Dashboard;

use App\Livewire\Dashboard\Index;
use App\Models\Account;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $account;

    protected function setUp(): void
    {
        parent::setUp();

        // Create account
        $this->account = Account::factory()->create();

        // Create user and attach to account
        $this->user = User::factory()->create();
        $this->user->accounts()->attach($this->account->id, ['is_owner' => true]);
    }

    /**
     * Test that activity times are displayed in PST timezone.
     *
     * @return void
     */
    public function test_activity_times_displayed_in_pst()
    {
        // Create an activity with a known UTC time
        $utcTime = Carbon::parse('2025-01-15 18:00:00', 'UTC'); // 6 PM UTC
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'created_at' => $utcTime,
        ]);

        // Expected PST time (10 AM PST on same day)
        $expectedPstTime = 'Jan 15, 2025 10:00 AM';

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee($expectedPstTime.' PST');
    }

    /**
     * Test timezone conversion during daylight saving time.
     *
     * @return void
     */
    public function test_activity_times_displayed_in_pdt()
    {
        // Create an activity with a known UTC time during daylight saving
        $utcTime = Carbon::parse('2025-07-15 18:00:00', 'UTC'); // 6 PM UTC
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'created_at' => $utcTime,
        ]);

        // Expected PDT time (11 AM PDT on same day)
        $expectedPdtTime = 'Jul 15, 2025 11:00 AM';

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee($expectedPdtTime.' PST'); // Still labeled as PST in the view
    }

    /**
     * Test that multiple activities show correct PST times.
     *
     * @return void
     */
    public function test_multiple_activities_show_correct_pst_times()
    {
        // Create activities at different UTC times
        Activity::factory()->create([
            'account_id' => $this->account->id,
            'created_at' => Carbon::parse('2025-01-15 08:00:00', 'UTC'), // 8 AM UTC = 12 AM PST
        ]);

        Activity::factory()->create([
            'account_id' => $this->account->id,
            'created_at' => Carbon::parse('2025-01-15 20:30:00', 'UTC'), // 8:30 PM UTC = 12:30 PM PST
        ]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee('Jan 15, 2025 12:00 AM PST')
            ->assertSee('Jan 15, 2025 12:30 PM PST');
    }
}
