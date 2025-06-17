<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\NoticeType;
use App\Models\Referral;
use App\Models\Referrer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_referral()
    {
        $referrer = Referrer::factory()->create();
        $account = Account::factory()->create();

        $referralData = [
            'referrer_id' => $referrer->id,
            'account_id' => $account->id,
            'discount_amount' => 5.00,
            'discount_percentage' => 33.33,
        ];

        $referral = Referral::create($referralData);

        $this->assertInstanceOf(Referral::class, $referral);
        $this->assertEquals($referrer->id, $referral->referrer_id);
        $this->assertEquals($account->id, $referral->account_id);
        $this->assertEquals(5.00, $referral->discount_amount);
        $this->assertEquals(33.33, $referral->discount_percentage);
    }

    #[Test]
    public function it_belongs_to_a_referrer()
    {
        $referrer = Referrer::factory()->create();
        $referral = Referral::factory()->create(['referrer_id' => $referrer->id]);

        $this->assertInstanceOf(Referrer::class, $referral->referrer);
        $this->assertEquals($referrer->id, $referral->referrer->id);
    }

    #[Test]
    public function it_belongs_to_an_account()
    {
        $account = Account::factory()->create();
        $referral = Referral::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $referral->account);
        $this->assertEquals($account->id, $referral->account->id);
    }

    #[Test]
    public function it_returns_applied_discount_from_stored_amount()
    {
        $referral = Referral::factory()->create([
            'discount_amount' => 7.50,
        ]);

        $this->assertEquals(7.50, $referral->applied_discount);
    }

    #[Test]
    public function it_returns_applied_discount_from_referrer_when_null()
    {
        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);

        // Create notice type for the referrer
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 10.00,
        ]);

        $referral = Referral::factory()->create([
            'referrer_id' => $referrer->id,
            'discount_amount' => null,
        ]);

        // When discount_amount is null, it should fall back to referrer's discount
        $this->assertEquals(5.00, $referral->applied_discount); // 15 - 10 = 5
    }

    #[Test]
    public function it_can_create_referral_from_referrer_and_account()
    {
        $referrer = Referrer::factory()->create([
            'plan_date' => '2025-01-01',
        ]);
        $account = Account::factory()->create();

        // Create notice type for the referrer
        NoticeType::factory()->create([
            'name' => '10-Day Notice',
            'plan_date' => '2025-01-01',
            'price' => 11.00,
        ]);

        $referral = Referral::createFromReferrer($referrer, $account);

        $this->assertInstanceOf(Referral::class, $referral);
        $this->assertEquals($referrer->id, $referral->referrer_id);
        $this->assertEquals($account->id, $referral->account_id);
        $this->assertEquals(4.00, $referral->discount_amount); // 15 - 11 = 4
        $this->assertEquals(26.67, $referral->discount_percentage); // 4/15 * 100 rounded
    }

    #[Test]
    public function it_calculates_discount_percentage_correctly()
    {
        $account = Account::factory()->create();

        // Test various discount amounts by creating different notice types
        $testCases = [
            ['price' => 13.50, 'expected_discount' => 1.50, 'expected_percentage' => 10.00],
            ['price' => 12.00, 'expected_discount' => 3.00, 'expected_percentage' => 20.00],
            ['price' => 7.50, 'expected_discount' => 7.50, 'expected_percentage' => 50.00],
        ];

        foreach ($testCases as $index => $case) {
            $planDate = '2025-0'.($index + 1).'-01';

            $referrer = Referrer::factory()->create([
                'plan_date' => $planDate,
            ]);

            NoticeType::factory()->create([
                'name' => '10-Day Notice',
                'plan_date' => $planDate,
                'price' => $case['price'],
            ]);

            $referral = Referral::createFromReferrer($referrer, $account);

            $this->assertEquals($case['expected_discount'], $referral->discount_amount);
            $this->assertEquals($case['expected_percentage'], $referral->discount_percentage);

            // Clean up for next iteration
            $referral->delete();
        }
    }

    #[Test]
    public function it_casts_discount_amounts_to_decimal()
    {
        $referral = Referral::factory()->create([
            'discount_amount' => '5.50',
            'discount_percentage' => '25.75',
        ]);

        // Laravel's decimal casting returns strings with proper decimal formatting
        $this->assertIsString($referral->discount_amount);
        $this->assertIsString($referral->discount_percentage);
        $this->assertEquals('5.50', $referral->discount_amount);
        $this->assertEquals('25.75', $referral->discount_percentage);
    }
}
