<?php

namespace Tests\Feature\Controllers;

use App\Models\NoticeType;
use App\Models\Referrer;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create default notice types for testing
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
    }

    #[Test]
    public function it_displays_referral_landing_page_for_active_referrer()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'slug' => 'john-doe',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        $response->assertSee('John Doe');
        $response->assertSee('Special Offer');
        $response->assertViewIs('marketing.referral');
        $response->assertViewHas('referrer', $referrer);
    }

    #[Test]
    public function it_returns_404_for_inactive_referrer()
    {
        $referrer = Referrer::factory()->create([
            'slug' => 'inactive-referrer',
            'is_active' => false,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_404_for_nonexistent_referrer()
    {
        $response = $this->get('/r/nonexistent-slug');

        $response->assertNotFound();
    }

    #[Test]
    public function it_sets_referrer_cookie_when_visiting_referral_page()
    {
        $referrer = Referrer::factory()->create([
            'slug' => 'test-referrer',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        
        // Check that cookie was queued
        $this->assertTrue(Cookie::hasQueued('referrer_id'));
        
        // Get the queued cookie
        $queuedCookies = Cookie::getQueuedCookies();
        $referrerCookie = collect($queuedCookies)->first(function ($cookie) {
            return $cookie->getName() === 'referrer_id';
        });

        $this->assertNotNull($referrerCookie);
        $this->assertEquals($referrer->id, $referrerCookie->getValue());
        $this->assertEquals(2592000, $referrerCookie->getMaxAge()); // 30 days in seconds
    }

    #[Test]
    public function it_displays_correct_pricing_information()
    {
        $referrer = Referrer::factory()->create([
            'slug' => 'pricing-test',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        
        // Check that pricing variables are passed to view
        $response->assertViewHas('standardPrice', 15.00);
        $response->assertViewHas('discountedPrice', 10.00);
        $response->assertViewHas('discountAmount', 5.00);
    }

    #[Test]
    public function it_displays_discount_percentage_correctly()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'slug' => 'jane-smith',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        
        // With $10 discounted price and $15 standard price, discount is 33%
        $response->assertSee('33%');
        $response->assertSee('Save 33');
    }

    #[Test]
    public function it_handles_referrer_names_ending_with_s_correctly()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'Spicer',
            'last_name' => 'Matthews',
            'slug' => 'spicer-matthews',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        
        // Should show "Matthews'" not "Matthews's"
        $response->assertSee("Matthews'");
        $response->assertDontSee("Matthews's");
        
        // Should show "Spicer'" not "Spicer's" since Spicer ends with 'r', not 's'
        $response->assertSee("Spicer's");
    }

    #[Test]
    public function it_handles_first_names_ending_with_s_correctly()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'James',
            'last_name' => 'Johnson',
            'slug' => 'james-johnson',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        
        // Should show "James'" not "James's"
        $response->assertSee("James'");
        $response->assertDontSee("James's");
    }

    #[Test]
    public function it_displays_referrer_name_prominently_throughout_page()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Wonder',
            'slug' => 'alice-wonder',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        
        // Check that referrer's name appears multiple times
        $content = $response->getContent();
        $aliceCount = substr_count($content, 'Alice');
        $wonderCount = substr_count($content, 'Wonder');
        
        // Should appear at least 5 times each (hero, discount banner, features, CTA, etc.)
        $this->assertGreaterThanOrEqual(5, $aliceCount);
        $this->assertGreaterThanOrEqual(3, $wonderCount);
    }

    #[Test]
    public function it_includes_video_tutorial_section()
    {
        $referrer = Referrer::factory()->create([
            'slug' => 'video-test',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        $response->assertSee('Watch Our Quick Tutorial');
    }

    #[Test]
    public function it_includes_features_section_with_referrer_context()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Builder',
            'slug' => 'bob-builder',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        $response->assertSee('Bob Builder');
    }

    #[Test]
    public function it_includes_call_to_action_with_referrer_discount()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'Carol',
            'last_name' => 'Singer',
            'slug' => 'carol-singer',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        $response->assertSee("Claim Carol");
        $response->assertSee("Sign Up With Carol");
    }

    #[Test]
    public function it_includes_proper_meta_tags_for_seo()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'David',
            'last_name' => 'Expert',
            'slug' => 'david-expert',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $response = $this->get("/r/{$referrer->slug}");

        $response->assertSuccessful();
        $response->assertSee('Special Offer from David Expert', false);
        $response->assertSee('David Expert has invited you to use Oregon Past Due Rent', false);
    }
}