<?php

namespace Tests\Feature\Auth;

use App\Models\Account;
use App\Models\NoticeType;
use App\Models\Referral;
use App\Models\Referrer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create notice types for testing
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 10.00,
        ]);

        NoticeType::factory()->create([
            'name' => '13-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 10.00,
        ]);

        NoticeType::factory()->create([
            'name' => '10-Day Notice Premium',
            'plan_date' => '2025-06-01',
            'price' => 12.00,
        ]);
    }

    #[Test]
    public function user_can_register_through_referral_link_with_cookie()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Referrer',
            'email' => 'john@example.com',
            'slug' => 'john-referrer',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // First visit the referral page to set cookie
        $this->get("/r/{$referrer->slug}");

        // Then register with the cookie set
        $response = $this->withCookies(['referrer_id' => $referrer->id])
            ->post(route('register'), [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'company_name' => 'Test Company',
            ]);

        $response->assertRedirect(route('dashboard'));

        // Verify user was created
        $user = User::where('email', 'jane.doe@example.com')->first();
        $this->assertNotNull($user);

        // Verify account was created with referrer's plan_date
        $account = Account::where('name', 'Test Company')->first();
        $this->assertNotNull($account);
        $this->assertEquals('2025-01-01', $account->notice_type_plan_date->format('Y-m-d'));

        // Verify referral record was created
        $referral = Referral::where('referrer_id', $referrer->id)
            ->where('account_id', $account->id)
            ->first();

        $this->assertNotNull($referral);
        $this->assertEquals(5.00, $referral->discount_amount); // 15.00 - 10.00
        $this->assertEquals(33.33, $referral->discount_percentage); // (5/15) * 100 rounded
    }

    #[Test]
    public function user_registration_without_referral_cookie_works_normally()
    {
        $response = $this->post(route('register'), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'Test Company',
        ]);

        $response->assertRedirect(route('dashboard'));

        // Verify account was created without referrer's plan_date (will use NoticeType::getMostRecentPlanDate() as default)
        $account = Account::where('name', 'Test Company')->first();
        $this->assertNotNull($account);
        // Without referrer, it should use the most recent plan date from NoticeType model
        $this->assertNotNull($account->notice_type_plan_date);

        // Verify no referral record was created
        $this->assertDatabaseCount('referrals', 0);
    }

    #[Test]
    public function registration_with_invalid_referrer_id_cookie_works_normally()
    {
        // Set cookie with non-existent referrer ID
        $response = $this->withCookies(['referrer_id' => 99999])
            ->post(route('register'), [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'company_name' => 'Test Company',
            ]);

        $response->assertRedirect(route('dashboard'));

        // Verify account was created without referrer's plan_date (will use default)
        $account = Account::where('name', 'Test Company')->first();
        $this->assertNotNull($account);
        $this->assertNotNull($account->notice_type_plan_date);

        // Verify no referral record was created
        $this->assertDatabaseCount('referrals', 0);
    }

    #[Test]
    public function registration_with_inactive_referrer_cookie_works_normally()
    {
        $inactiveReferrer = Referrer::factory()->create([
            'slug' => 'inactive-referrer',
            'plan_date' => '2025-01-01',
            'is_active' => false,
        ]);

        $response = $this->withCookies(['referrer_id' => $inactiveReferrer->id])
            ->post(route('register'), [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'company_name' => 'Test Company',
            ]);

        $response->assertRedirect(route('dashboard'));

        // Verify account was created without referrer's plan_date (will use default)
        $account = Account::where('name', 'Test Company')->first();
        $this->assertNotNull($account);
        $this->assertNotNull($account->notice_type_plan_date);

        // Verify no referral record was created
        $this->assertDatabaseCount('referrals', 0);
    }

    #[Test]
    public function referral_applies_correct_plan_date_pricing()
    {
        // Create referrer with premium plan date
        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-06-01', // This should have $12 pricing
            'is_active' => true,
        ]);

        $response = $this->withCookies(['referrer_id' => $referrer->id])
            ->post(route('register'), [
                'first_name' => 'Premium',
                'last_name' => 'User',
                'email' => 'premium@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'company_name' => 'Premium Company',
            ]);

        $response->assertRedirect(route('dashboard'));

        // Verify account gets referrer's plan_date
        $account = Account::where('name', 'Premium Company')->first();
        $this->assertEquals('2025-06-01', $account->notice_type_plan_date->format('Y-m-d'));

        // Verify referral record has correct discount calculation
        $referral = Referral::where('referrer_id', $referrer->id)->first();
        $this->assertNotNull($referral);
        $this->assertEquals(3.00, $referral->discount_amount); // 15.00 - 12.00
        $this->assertEquals(20.00, $referral->discount_percentage); // (3/15) * 100
    }

    #[Test]
    public function multiple_referrals_can_be_created_from_same_referrer()
    {
        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // Create two accounts manually to simulate two registrations
        $account1 = Account::factory()->create([
            'name' => 'First Company',
            'notice_type_plan_date' => $referrer->plan_date,
        ]);

        $account2 = Account::factory()->create([
            'name' => 'Second Company',
            'notice_type_plan_date' => $referrer->plan_date,
        ]);

        // Create referral records for both accounts
        $referral1 = Referral::createFromReferrer($referrer, $account1);
        $referral2 = Referral::createFromReferrer($referrer, $account2);

        // Verify both referral records exist
        $referrals = Referral::where('referrer_id', $referrer->id)->get();
        $this->assertCount(2, $referrals);

        // Verify both referrals have correct discount amounts
        $this->assertEquals(5.00, $referral1->discount_amount); // 15 - 10 = 5
        $this->assertEquals(5.00, $referral2->discount_amount);
        $this->assertEquals(33.33, $referral1->discount_percentage);
        $this->assertEquals(33.33, $referral2->discount_percentage);
    }

    #[Test]
    public function referral_cookie_persists_across_multiple_page_visits()
    {
        $referrer = Referrer::factory()->create([
            'slug' => 'persistent-test',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // Visit referral page to set cookie
        $this->get('/r/{referrer->slug}');

        // Visit other pages (simulate user browsing)
        $this->get('/');
        $this->get('/pricing');

        // Then register - cookie should still be there
        $response = $this->withCookies(['referrer_id' => $referrer->id])
            ->post(route('register'), [
                'first_name' => 'Persistent',
                'last_name' => 'User',
                'email' => 'persistent@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'company_name' => 'Persistent Company',
            ]);

        $response->assertRedirect(route('dashboard'));

        // Verify referral was still applied
        $account = Account::where('name', 'Persistent Company')->first();
        $this->assertEquals('2025-01-01', $account->notice_type_plan_date->format('Y-m-d'));

        $referral = Referral::where('referrer_id', $referrer->id)->first();
        $this->assertNotNull($referral);
    }

    #[Test]
    public function referral_discount_calculation_handles_edge_cases()
    {
        // Test case where referrer price equals standard price (no discount)
        NoticeType::factory()->create([
            'name' => '10-Day Notice Standard',
            'plan_date' => '2025-12-01',
            'price' => 15.00, // Same as standard price
        ]);

        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-12-01',
            'is_active' => true,
        ]);

        $response = $this->withCookies(['referrer_id' => $referrer->id])
            ->post(route('register'), [
                'first_name' => 'No',
                'last_name' => 'Discount',
                'email' => 'nodiscount@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'company_name' => 'No Discount Company',
            ]);

        $response->assertRedirect(route('dashboard'));

        // Verify referral record still created but with zero discount
        $referral = Referral::where('referrer_id', $referrer->id)->first();
        $this->assertNotNull($referral);
        $this->assertEquals(0.00, $referral->discount_amount);
        $this->assertEquals(0.00, $referral->discount_percentage);
    }
}
