<?php

namespace Tests\Feature\Livewire\Notices;

use App\Livewire\Notices\Preview;
use App\Models\Account;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PreviewTest extends TestCase
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

        // Set the current user's account to fix authentication issues
        $this->user->account = $this->account;
    }

    #[Test]
    public function preview_redirects_if_notice_is_not_pending_payment()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create a notice with draft status
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'draft',
            'notice_type_id' => $noticeType->id,
        ]);

        // Access the preview page for this notice
        $response = $this->actingAs($this->user)
            ->get(route('notices.preview', $notice->id));

        // Should be redirected to notices.index with an error message
        $response->assertRedirect(route('notices.index'));
        $response->assertSessionHas('error', 'This notice is no longer in pending payment status.');
    }

    #[Test]
    public function preview_shows_for_pending_payment_notice()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create(['name' => 'Test Notice Type']);

        // Create a notice with pending payment status
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
            'notice_type_id' => $noticeType->id,
        ]);

        // Load the preview page
        $response = $this->actingAs($this->user)
            ->get(route('notices.preview', $notice->id));

        $response->assertSuccessful();
        $response->assertSee('Notice Preview');
        $response->assertSee('Proceed to Payment');
    }

    #[Test]
    public function back_to_edit_button_redirects_to_edit_page()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create a notice with pending payment status
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
            'notice_type_id' => $noticeType->id,
        ]);

        // Test the backToEdit method
        Livewire::actingAs($this->user)
            ->test(Preview::class, ['notice' => $notice])
            ->call('backToEdit')
            ->assertRedirect(route('notices.edit', $notice->id));
    }

    #[Test]
    public function keep_as_draft_redirects_to_index_page_with_notice_message()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create a notice with pending payment status
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
            'notice_type_id' => $noticeType->id,
        ]);

        // Test the keepAsDraft method
        Livewire::actingAs($this->user)
            ->test(Preview::class, ['notice' => $notice])
            ->call('keepAsDraft')
            ->assertRedirect(route('notices.index'))
            ->assertSessionHas('notice', 'Notice has been kept as a draft.');
    }

    #[Test]
    public function save_as_draft_redirects_to_index_page_with_success_message()
    {
        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create a notice with pending payment status
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
            'notice_type_id' => $noticeType->id,
        ]);

        // Test the saveAsDraft method
        Livewire::actingAs($this->user)
            ->test(Preview::class, ['notice' => $notice])
            ->call('saveAsDraft')
            ->assertRedirect(route('notices.index'))
            ->assertSessionHas('success', 'Notice kept as a draft successfully.');
    }
}
