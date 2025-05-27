<?php

namespace Tests\Feature\Notices;

use App\Livewire\Notices\Show;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\NoticeServed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NoticeServedTest extends TestCase
{
    use RefreshDatabase;

    private Account $account;

    private User $user;

    private User $superAdmin;

    private Notice $notice;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        $this->user->accounts()->attach($this->account->id);

        $this->superAdmin = User::factory()->create(['type' => User::TYPE_SUPER_ADMIN]);
        $this->superAdmin->accounts()->attach($this->account->id);

        // Create notice type
        $noticeType = NoticeType::factory()->create([
            'name' => '10 Day Notice',
            'price' => 50.00,
        ]);

        // Create agent
        $agent = Agent::factory()->create([
            'account_id' => $this->account->id,
        ]);

        // Create notice
        $this->notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $noticeType->id,
            'status' => Notice::STATUS_SERVICE_PENDING,
        ]);

        // Create and attach tenant
        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id,
        ]);
        $this->notice->tenants()->attach($tenant->id);
    }

    #[Test]
    public function super_admin_can_upload_certificate_and_trigger_email_notification()
    {
        Storage::fake('s3');
        Notification::fake();

        $this->actingAs($this->superAdmin);

        $file = UploadedFile::fake()->create('certificate.pdf', 1000, 'application/pdf');

        Livewire::test(Show::class, ['notice' => $this->notice])
            ->set('certificatePdf', $file)
            ->call('uploadCertificatePdf')
            ->assertSet('uploadSuccess', true);

        // Assert the file was stored
        Storage::disk('s3')->assertExists($this->account->id.'/certificate_'.$this->notice->id.'.pdf');

        // Assert the notice was updated
        $this->assertDatabaseHas('notices', [
            'id' => $this->notice->id,
            'status' => Notice::STATUS_SERVED,
        ]);

        // Assert the notification was sent
        Notification::assertSentTo(
            [$this->user],
            NoticeServed::class,
            function ($notification, $channels) {
                return $notification->notice->id === $this->notice->id
                    && in_array('mail', $channels);
            }
        );
    }

    #[Test]
    public function regular_user_cannot_upload_certificate()
    {
        Storage::fake('s3');
        Notification::fake();

        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('certificate.pdf', 1000, 'application/pdf');

        Livewire::test(Show::class, ['notice' => $this->notice])
            ->set('certificatePdf', $file)
            ->call('uploadCertificatePdf')
            ->assertStatus(403);

        // Assert no notification was sent
        Notification::assertNotSentTo([$this->user], NoticeServed::class);
    }

    #[Test]
    public function impersonating_user_can_upload_certificate_and_trigger_email()
    {
        Storage::fake('s3');
        Notification::fake();

        // Simulate impersonation
        session(['impersonating' => $this->user->id]);
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('certificate.pdf', 1000, 'application/pdf');

        Livewire::test(Show::class, ['notice' => $this->notice])
            ->set('certificatePdf', $file)
            ->call('uploadCertificatePdf')
            ->assertSet('uploadSuccess', true);

        // Assert the notification was sent
        Notification::assertSentTo(
            [$this->user],
            NoticeServed::class,
            function ($notification) {
                return $notification->notice->id === $this->notice->id;
            }
        );
    }

    #[Test]
    public function email_contains_correct_information()
    {
        Notification::fake();

        $notification = new NoticeServed($this->notice);
        $mailable = $notification->toMail($this->user);

        // Get the rendered content
        $rendered = $mailable->render();

        // Assert email contains expected content
        $this->assertStringContainsString($this->notice->noticeType->name, $rendered);
        $this->assertStringContainsString($this->notice->tenants->first()->full_name, $rendered);
        $this->assertStringContainsString('Notice Successfully Served', $rendered);
        $this->assertStringContainsString('certificate of mailing', $rendered);
    }
}
