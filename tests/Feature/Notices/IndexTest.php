<?php

namespace Tests\Feature\Livewire\Notices;

use App\Models\Account;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account and a user associated with that account
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        $this->user->accounts()->attach($this->account);
    }

    #[Test]
    public function notices_index_shows_proceed_to_payment_button_for_pending_payment_status()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create a notice with pending_payment status
        $pendingPaymentNotice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
            'notice_type_id' => $noticeType->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('notices.index'));

        $response->assertSuccessful();
        $response->assertSee('Pending payment');
        $response->assertSee('Proceed to Payment');
        $response->assertSee(route('notices.preview', $pendingPaymentNotice->id));
    }

    #[Test]
    public function notices_index_shows_view_button_for_non_pending_payment_status()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create notices with different statuses
        $draftNotice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'draft',
            'notice_type_id' => $noticeType->id,
        ]);

        $servicePendingNotice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'service_pending',
            'notice_type_id' => $noticeType->id,
        ]);

        $servedNotice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'served',
            'notice_type_id' => $noticeType->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('notices.index'));

        $response->assertSuccessful();

        // Check for draft notice
        $response->assertSee(route('notices.show', $draftNotice->id));

        // Check for service pending notice
        $response->assertSee(route('notices.show', $servicePendingNotice->id));

        // Check for served notice
        $response->assertSee(route('notices.show', $servedNotice->id));

        // Should see Edit button only for draft notices
        $response->assertSee(route('notices.edit', $draftNotice->id));
        $response->assertDontSee(route('notices.edit', $servicePendingNotice->id));
        $response->assertDontSee(route('notices.edit', $servedNotice->id));
    }
}
