<?php

namespace Tests\Unit\Console\Commands;

use App\Models\Referrer;
use App\Models\RealtorList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportRealtorsToReferrersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test command imports realtors successfully
     */
    public function test_imports_realtors_successfully()
    {
        // Create test realtors
        RealtorList::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'full_name' => 'John Doe',
        ]);

        RealtorList::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'full_name' => 'Jane Smith',
        ]);

        $this->artisan('import:realtors-to-referrers')
            ->expectsOutput('Starting realtor to referrer import...')
            ->assertExitCode(0);

        // Verify referrers were created
        $this->assertEquals(2, Referrer::count());

        $johnReferrer = Referrer::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($johnReferrer);
        $this->assertEquals('John', $johnReferrer->first_name);
        $this->assertEquals('Doe', $johnReferrer->last_name);
        $this->assertEquals('john-doe', $johnReferrer->slug);
        $this->assertEquals('2024-01-03', $johnReferrer->plan_date->format('Y-m-d'));
        $this->assertTrue($johnReferrer->is_active);

        $janeReferrer = Referrer::where('email', 'jane.smith@example.com')->first();
        $this->assertNotNull($janeReferrer);
        $this->assertEquals('jane-smith', $janeReferrer->slug);
    }

    /**
     * Test command skips realtors with missing data
     */
    public function test_skips_realtors_with_missing_data()
    {
        // Create realtors with missing required fields
        RealtorList::create([
            'first_name' => null,
            'last_name' => 'Doe',
            'email' => 'incomplete1@example.com',
            'full_name' => 'Doe',
        ]);

        RealtorList::create([
            'first_name' => 'Jane',
            'last_name' => null,
            'email' => 'incomplete2@example.com',
            'full_name' => 'Jane',
        ]);

        RealtorList::create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => null,
            'full_name' => 'Bob Wilson',
        ]);

        // Create one complete realtor
        RealtorList::create([
            'first_name' => 'Complete',
            'last_name' => 'User',
            'email' => 'complete@example.com',
            'full_name' => 'Complete User',
        ]);

        $this->artisan('import:realtors-to-referrers')
            ->assertExitCode(0);

        // Only the complete realtor should be imported
        $this->assertEquals(1, Referrer::count());
        $this->assertEquals('complete@example.com', Referrer::first()->email);
    }

    /**
     * Test command skips existing referrers
     */
    public function test_skips_existing_referrers()
    {
        // Create an existing referrer
        Referrer::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'slug' => 'existing-user',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        // Create realtors - one with same email as existing referrer
        RealtorList::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'full_name' => 'Existing User',
        ]);

        RealtorList::create([
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'new@example.com',
            'full_name' => 'New User',
        ]);

        $this->artisan('import:realtors-to-referrers')
            ->expectsOutput('Skipping existing@example.com - already exists as referrer')
            ->assertExitCode(0);

        // Should still only have 2 referrers (1 existing + 1 new)
        $this->assertEquals(2, Referrer::count());
        $this->assertNotNull(Referrer::where('email', 'new@example.com')->first());
    }

    /**
     * Test slug generation strategies
     */
    public function test_slug_generation_strategies()
    {
        // Create existing referrers to test slug collision handling
        Referrer::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'existing1@example.com',
            'slug' => 'test-user', // Strategy 1: firstname-lastname
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Another',
            'last_name' => 'Person',
            'email' => 'existing2@example.com',
            'slug' => 'user-test', // Strategy 2: lastname-firstname
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Some',
            'last_name' => 'Guy',
            'email' => 'existing3@example.com',
            'slug' => 'user', // Strategy 3: lastname only
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Other',
            'last_name' => 'Person',
            'email' => 'existing4@example.com',
            'slug' => 'test', // Strategy 4: firstname only
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        // Create realtor that will hit all slug collisions
        RealtorList::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'new.test.user@example.com',
            'full_name' => 'Test User',
        ]);

        $this->artisan('import:realtors-to-referrers')
            ->assertExitCode(0);

        // New referrer should be created with random number suffix
        $newReferrer = Referrer::where('email', 'new.test.user@example.com')->first();
        $this->assertNotNull($newReferrer);
        
        // Should match pattern: test-user-{4 digit number}
        $this->assertMatchesRegularExpression('/^test-user-\d{4}$/', $newReferrer->slug);
    }

    /**
     * Test command handles special characters in names
     */
    public function test_handles_special_characters_in_names()
    {
        RealtorList::create([
            'first_name' => "John O'Connor",
            'last_name' => 'Smith-Jones',
            'email' => 'john.oconnor@example.com',
            'full_name' => "John O'Connor Smith-Jones",
        ]);

        $this->artisan('import:realtors-to-referrers')
            ->assertExitCode(0);

        $referrer = Referrer::where('email', 'john.oconnor@example.com')->first();
        $this->assertNotNull($referrer);
        $this->assertEquals('john-oconnor-smith-jones', $referrer->slug);
    }

    /**
     * Test command processes large batches
     */
    public function test_processes_large_batches()
    {
        // Create 150 realtors to test chunking
        for ($i = 1; $i <= 150; $i++) {
            RealtorList::create([
                'first_name' => "First{$i}",
                'last_name' => "Last{$i}",
                'email' => "realtor{$i}@example.com",
                'full_name' => "First{$i} Last{$i}",
            ]);
        }

        $this->artisan('import:realtors-to-referrers')
            ->assertExitCode(0);

        $this->assertEquals(150, Referrer::count());

        // Check first and last
        $this->assertNotNull(Referrer::where('email', 'realtor1@example.com')->first());
        $this->assertNotNull(Referrer::where('email', 'realtor150@example.com')->first());
    }

    /**
     * Test command shows progress and summary
     */
    public function test_shows_progress_and_summary()
    {
        // Create test data
        RealtorList::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'full_name' => 'John Doe',
        ]);

        // Create existing referrer to test skip
        Referrer::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'slug' => 'existing-user',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        RealtorList::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'full_name' => 'Existing User',
        ]);

        $this->artisan('import:realtors-to-referrers')
            ->expectsOutput('Import completed!')
            ->expectsOutputToContain('Imported: John Doe (john@example.com)')
            ->expectsOutputToContain('Skipping existing@example.com - already exists')
            ->assertExitCode(0);
    }
}