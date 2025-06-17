<?php

namespace Tests\Unit\Models;

use App\Models\NoticeType;
use App\Models\Referral;
use App\Models\Referrer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferrerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_referrer()
    {
        $referrerData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'slug' => 'john-doe',
            'plan_date' => '2025-01-01',
            'is_active' => true,
        ];

        $referrer = Referrer::create($referrerData);

        $this->assertInstanceOf(Referrer::class, $referrer);
        $this->assertEquals('John', $referrer->first_name);
        $this->assertEquals('Doe', $referrer->last_name);
        $this->assertEquals('john.doe@example.com', $referrer->email);
        $this->assertEquals('john-doe', $referrer->slug);
        $this->assertTrue($referrer->is_active);
    }

    #[Test]
    public function it_returns_full_name_attribute()
    {
        $referrer = Referrer::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $this->assertEquals('Jane Smith', $referrer->full_name);
    }

    #[Test]
    public function it_returns_referral_url_attribute()
    {
        $referrer = Referrer::factory()->create([
            'slug' => 'test-slug',
        ]);

        $expectedUrl = url('/r/test-slug');
        $this->assertEquals($expectedUrl, $referrer->referral_url);
    }

    #[Test]
    public function it_generates_unique_slug()
    {
        // Create existing referrer
        Referrer::factory()->create(['slug' => 'john-doe']);

        $slug = Referrer::generateUniqueSlug('John', 'Doe');

        $this->assertEquals('john-doe-1', $slug);
    }

    #[Test]
    public function it_generates_sequential_unique_slugs()
    {
        // Create existing referrers
        Referrer::factory()->create(['slug' => 'john-doe']);
        Referrer::factory()->create(['slug' => 'john-doe-1']);

        $slug = Referrer::generateUniqueSlug('John', 'Doe');

        $this->assertEquals('john-doe-2', $slug);
    }

    #[Test]
    public function it_returns_referrer_price_from_notice_type()
    {
        // Create a notice type for the referrer's plan date
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 10.00,
        ]);

        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);

        $this->assertEquals(10.00, $referrer->referrer_price);
    }

    #[Test]
    public function it_returns_default_price_when_no_notice_type_found()
    {
        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);

        // No matching notice type exists
        $this->assertEquals(15.00, $referrer->referrer_price);
    }

    #[Test]
    public function it_calculates_discount_amount()
    {
        // Create a notice type for the referrer's plan date
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 10.00,
        ]);

        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);

        // Standard price is 15.00, referrer price is 10.00, so discount is 5.00
        $this->assertEquals(5.00, $referrer->discount_amount);
    }

    #[Test]
    public function it_returns_zero_discount_when_referrer_price_equals_standard()
    {
        // Create a notice type with standard price
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 15.00,
        ]);

        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);

        $this->assertEquals(0.00, $referrer->discount_amount);
    }

    #[Test]
    public function it_returns_discounted_price_same_as_referrer_price()
    {
        // Create a notice type for the referrer's plan date
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 12.00,
        ]);

        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);

        $this->assertEquals(12.00, $referrer->discounted_price);
        $this->assertEquals($referrer->referrer_price, $referrer->discounted_price);
    }

    #[Test]
    public function it_has_referrals_relationship()
    {
        $referrer = Referrer::factory()->create();
        $referral = Referral::factory()->create(['referrer_id' => $referrer->id]);

        $this->assertTrue($referrer->referrals->contains($referral));
        $this->assertInstanceOf(Referral::class, $referrer->referrals->first());
    }

    #[Test]
    public function it_filters_active_referrers()
    {
        $activeReferrer = Referrer::factory()->create(['is_active' => true]);
        $inactiveReferrer = Referrer::factory()->create(['is_active' => false]);

        $activeReferrers = Referrer::active()->get();

        $this->assertTrue($activeReferrers->contains($activeReferrer));
        $this->assertFalse($activeReferrers->contains($inactiveReferrer));
    }
}
