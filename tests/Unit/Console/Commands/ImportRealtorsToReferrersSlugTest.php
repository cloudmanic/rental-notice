<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\ImportRealtorsToReferrers;
use App\Models\Referrer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class ImportRealtorsToReferrersSlugTest extends TestCase
{
    use RefreshDatabase;

    private ImportRealtorsToReferrers $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new ImportRealtorsToReferrers();
    }

    /**
     * Test slug generation strategy 1: firstname-lastname
     */
    public function test_slug_generation_strategy_1_firstname_lastname()
    {
        $slug = $this->callGenerateSlugMethod('John', 'Doe');
        
        $this->assertEquals('john-doe', $slug);
    }

    /**
     * Test slug generation strategy 2: lastname-firstname when firstname-lastname exists
     */
    public function test_slug_generation_strategy_2_lastname_firstname()
    {
        // Create existing referrer with firstname-lastname
        Referrer::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'slug' => 'john-doe',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        $slug = $this->callGenerateSlugMethod('John', 'Doe');
        
        $this->assertEquals('doe-john', $slug);
    }

    /**
     * Test slug generation strategy 3: lastname only when first two strategies exist
     */
    public function test_slug_generation_strategy_3_lastname_only()
    {
        // Create existing referrers with first two strategies
        Referrer::create([
            'first_name' => 'Test1',
            'last_name' => 'User',
            'email' => 'test1@example.com',
            'slug' => 'john-doe',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Test2',
            'last_name' => 'User',
            'email' => 'test2@example.com',
            'slug' => 'doe-john',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        $slug = $this->callGenerateSlugMethod('John', 'Doe');
        
        $this->assertEquals('doe', $slug);
    }

    /**
     * Test slug generation strategy 4: firstname only when first three strategies exist
     */
    public function test_slug_generation_strategy_4_firstname_only()
    {
        // Create existing referrers with first three strategies
        Referrer::create([
            'first_name' => 'Test1',
            'last_name' => 'User',
            'email' => 'test1@example.com',
            'slug' => 'john-doe',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Test2',
            'last_name' => 'User',
            'email' => 'test2@example.com',
            'slug' => 'doe-john',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Test3',
            'last_name' => 'User',
            'email' => 'test3@example.com',
            'slug' => 'doe',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        $slug = $this->callGenerateSlugMethod('John', 'Doe');
        
        $this->assertEquals('john', $slug);
    }

    /**
     * Test slug generation strategy 5: firstname-lastname-random when all others exist
     */
    public function test_slug_generation_strategy_5_random_number()
    {
        // Create existing referrers with all four strategies
        Referrer::create([
            'first_name' => 'Test1',
            'last_name' => 'User',
            'email' => 'test1@example.com',
            'slug' => 'john-doe',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Test2',
            'last_name' => 'User',
            'email' => 'test2@example.com',
            'slug' => 'doe-john',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Test3',
            'last_name' => 'User',
            'email' => 'test3@example.com',
            'slug' => 'doe',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        Referrer::create([
            'first_name' => 'Test4',
            'last_name' => 'User',
            'email' => 'test4@example.com',
            'slug' => 'john',
            'plan_date' => '2024-01-03',
            'is_active' => true,
        ]);

        $slug = $this->callGenerateSlugMethod('John', 'Doe');
        
        // Should match pattern: john-doe-{4 digit number}
        $this->assertMatchesRegularExpression('/^john-doe-\d{4}$/', $slug);
        
        // Verify the slug is unique
        $this->assertFalse(Referrer::where('slug', $slug)->exists());
    }

    /**
     * Test slug generation handles special characters
     */
    public function test_slug_generation_handles_special_characters()
    {
        $testCases = [
            ["John O'Connor", "Smith-Jones", "john-oconnor-smith-jones"],
            ["Mary-Jane", "O'Sullivan", "mary-jane-osullivan"],
            ["José", "García", "jose-garcia"],
            ["Anne-Marie", "Van Der Berg", "anne-marie-van-der-berg"],
            ["Jean-Claude", "St. Pierre", "jean-claude-st-pierre"],
            ["O'Malley", "McDonald's", "omalley-mcdonalds"],
        ];

        foreach ($testCases as [$firstName, $lastName, $expectedSlug]) {
            $slug = $this->callGenerateSlugMethod($firstName, $lastName);
            $this->assertEquals($expectedSlug, $slug, 
                "Expected '{$expectedSlug}' for '{$firstName} {$lastName}', got '{$slug}'");
        }
    }

    /**
     * Test slug generation with empty or whitespace names
     */
    public function test_slug_generation_with_edge_case_names()
    {
        $testCases = [
            ["John", "  ", "john-"], // Whitespace in last name
            ["  ", "Doe", "-doe"], // Whitespace in first name
            ["A", "B", "a-b"], // Single characters
            ["123John", "456Doe", "123john-456doe"], // Numbers in names
            ["John!!!", "Doe???", "john-doe"], // Special punctuation
        ];

        foreach ($testCases as [$firstName, $lastName, $expectedSlug]) {
            $slug = $this->callGenerateSlugMethod($firstName, $lastName);
            $this->assertEquals($expectedSlug, $slug,
                "Expected '{$expectedSlug}' for '{$firstName}' + '{$lastName}', got '{$slug}'");
        }
    }

    /**
     * Test random number uniqueness in strategy 5
     */
    public function test_random_number_uniqueness_in_strategy_5()
    {
        // Create all base strategies
        Referrer::create([
            'first_name' => 'Test1', 'last_name' => 'User', 'email' => 'test1@example.com',
            'slug' => 'test-user', 'plan_date' => '2024-01-03', 'is_active' => true,
        ]);
        Referrer::create([
            'first_name' => 'Test2', 'last_name' => 'User', 'email' => 'test2@example.com',
            'slug' => 'user-test', 'plan_date' => '2024-01-03', 'is_active' => true,
        ]);
        Referrer::create([
            'first_name' => 'Test3', 'last_name' => 'User', 'email' => 'test3@example.com',
            'slug' => 'user', 'plan_date' => '2024-01-03', 'is_active' => true,
        ]);
        Referrer::create([
            'first_name' => 'Test4', 'last_name' => 'User', 'email' => 'test4@example.com',
            'slug' => 'test', 'plan_date' => '2024-01-03', 'is_active' => true,
        ]);

        // Generate multiple slugs and ensure they're all unique
        $generatedSlugs = [];
        for ($i = 0; $i < 10; $i++) {
            $slug = $this->callGenerateSlugMethod('Test', 'User');
            $this->assertNotContains($slug, $generatedSlugs, "Duplicate slug generated: {$slug}");
            $generatedSlugs[] = $slug;
            
            // Create the referrer to ensure next iteration gets a different slug
            Referrer::create([
                'first_name' => "Test{$i}",
                'last_name' => 'User',
                'email' => "test-unique-{$i}@example.com", // Make email unique
                'slug' => $slug,
                'plan_date' => '2024-01-03',
                'is_active' => true,
            ]);
        }

        // All generated slugs should follow the pattern
        foreach ($generatedSlugs as $slug) {
            $this->assertMatchesRegularExpression('/^test-user-\d{4}$/', $slug);
        }
    }

    /**
     * Test slug generation with unicode characters
     */
    public function test_slug_generation_with_unicode_characters()
    {
        $testCases = [
            ["José", "García", "jose-garcia"],
            ["François", "Müller", "francois-muller"],
            ["Søren", "Åström", "soren-astrom"],
            ["Ñoño", "Peña", "nono-pena"],
            ["Владимир", "Путин", "vladimir-putin"], // Cyrillic
        ];

        foreach ($testCases as [$firstName, $lastName, $expectedSlug]) {
            $slug = $this->callGenerateSlugMethod($firstName, $lastName);
            $this->assertEquals($expectedSlug, $slug,
                "Expected '{$expectedSlug}' for '{$firstName} {$lastName}', got '{$slug}'");
        }
    }

    /**
     * Test case sensitivity in slug generation
     */
    public function test_slug_generation_case_insensitive()
    {
        $testCases = [
            ["JOHN", "DOE", "john-doe"],
            ["john", "doe", "john-doe"],
            ["John", "Doe", "john-doe"],
            ["jOhN", "DoE", "john-doe"],
        ];

        foreach ($testCases as [$firstName, $lastName, $expectedSlug]) {
            $slug = $this->callGenerateSlugMethod($firstName, $lastName);
            $this->assertEquals($expectedSlug, $slug);
        }
    }

    /**
     * Test slug generation performance with many existing slugs
     */
    public function test_slug_generation_performance_with_many_conflicts()
    {
        // Create many conflicting slugs to test random number generation efficiency
        $baseSlug = 'test-user';
        for ($i = 1000; $i <= 1050; $i++) {
            Referrer::create([
                'first_name' => "Test{$i}",
                'last_name' => 'User',
                'email' => "test{$i}@example.com",
                'slug' => "{$baseSlug}-{$i}",
                'plan_date' => '2024-01-03',
                'is_active' => true,
            ]);
        }

        // Also create the base strategies
        Referrer::create(['first_name' => 'X', 'last_name' => 'Y', 'email' => 'x@example.com', 'slug' => 'test-user', 'plan_date' => '2024-01-03', 'is_active' => true]);
        Referrer::create(['first_name' => 'X', 'last_name' => 'Y', 'email' => 'y@example.com', 'slug' => 'user-test', 'plan_date' => '2024-01-03', 'is_active' => true]);
        Referrer::create(['first_name' => 'X', 'last_name' => 'Y', 'email' => 'z@example.com', 'slug' => 'user', 'plan_date' => '2024-01-03', 'is_active' => true]);
        Referrer::create(['first_name' => 'X', 'last_name' => 'Y', 'email' => 'a@example.com', 'slug' => 'test', 'plan_date' => '2024-01-03', 'is_active' => true]);

        $start = microtime(true);
        $slug = $this->callGenerateSlugMethod('Test', 'User');
        $end = microtime(true);

        // Should complete within reasonable time (1 second)
        $this->assertLessThan(1, $end - $start, 'Slug generation should complete within 1 second');
        
        // Should generate a unique slug not in the 1000-1050 range
        $this->assertMatchesRegularExpression('/^test-user-\d{4}$/', $slug);
        $this->assertFalse(Referrer::where('slug', $slug)->exists());
        
        // Extract the number and ensure it's not in our conflict range
        preg_match('/test-user-(\d{4})/', $slug, $matches);
        $number = (int) $matches[1];
        $this->assertTrue($number < 1000 || $number > 1050, 
            "Generated number {$number} should not be in conflict range 1000-1050");
    }

    /**
     * Helper method to call the private generateCustomUniqueSlug method
     */
    private function callGenerateSlugMethod(string $firstName, string $lastName): string
    {
        $reflection = new ReflectionClass($this->command);
        $method = $reflection->getMethod('generateCustomUniqueSlug');
        $method->setAccessible(true);
        
        return $method->invokeArgs($this->command, [$firstName, $lastName]);
    }
}