<?php

namespace Tests\Feature\Realtors;

use App\Livewire\Realtors\Index;
use App\Models\RealtorList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'type' => User::TYPE_SUPER_ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'type' => User::TYPE_CONTRIBUTOR,
        ]);
    }

    /**
     * Test super admin can access realtor index
     */
    public function test_super_admin_can_access_realtor_index()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Realtors')
            ->assertSee('Export CSV');
    }

    /**
     * Test regular user cannot access realtor index via route
     */
    public function test_regular_user_cannot_access_realtor_index()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('realtors.index'));
        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access realtor index via route
     */
    public function test_guest_cannot_access_realtor_index()
    {
        $response = $this->get(route('realtors.index'));
        $response->assertRedirect('/login'); // Should redirect to login
    }

    /**
     * Test realtor list displays correctly
     */
    public function test_realtor_list_displays_correctly()
    {
        $this->actingAs($this->superAdmin);

        // Create test realtors
        $realtor1 = RealtorList::create([
            'email' => 'john.doe@example.com',
            'full_name' => 'John Doe',
            'office_name' => 'Test Realty',
            'city' => 'Portland',
            'state' => 'OR',
            'phone' => '503-555-1234',
            'license_number' => 'LIC123456',
        ]);

        $realtor2 = RealtorList::create([
            'email' => 'jane.smith@example.com',
            'full_name' => 'Jane Smith',
            'office_name' => 'Smith Properties',
            'city' => 'Eugene',
            'state' => 'OR',
            'phone' => '541-555-5678',
            'license_number' => 'LIC789012',
        ]);

        Livewire::test(Index::class)
            ->assertSee('John Doe')
            ->assertSee('jane.smith@example.com')
            ->assertSee('Test Realty')
            ->assertSee('Portland')
            ->assertSee('LIC123456')
            ->assertSee('Jane Smith')
            ->assertSee('Smith Properties')
            ->assertSee('Eugene')
            ->assertSee('Total: 2 realtors');
    }

    /**
     * Test search functionality
     */
    public function test_search_functionality()
    {
        $this->actingAs($this->superAdmin);

        // Create test realtors
        RealtorList::create([
            'email' => 'john.doe@example.com',
            'full_name' => 'John Doe',
            'office_name' => 'Test Realty',
            'city' => 'Portland',
            'state' => 'OR',
        ]);

        RealtorList::create([
            'email' => 'jane.smith@example.com',
            'full_name' => 'Jane Smith',
            'office_name' => 'Smith Properties',
            'city' => 'Eugene',
            'state' => 'OR',
        ]);

        // Test search by name
        Livewire::test(Index::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');

        // Test search by email
        Livewire::test(Index::class)
            ->set('search', 'jane.smith')
            ->assertSee('Jane Smith')
            ->assertDontSee('John Doe');

        // Test search by city
        Livewire::test(Index::class)
            ->set('search', 'Eugene')
            ->assertSee('Jane Smith')
            ->assertDontSee('John Doe');

        // Test search by office
        Livewire::test(Index::class)
            ->set('search', 'Test Realty')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');

        // Test empty search shows all
        Livewire::test(Index::class)
            ->set('search', '')
            ->assertSee('John Doe')
            ->assertSee('Jane Smith');
    }

    /**
     * Test sorting functionality
     */
    public function test_sorting_functionality()
    {
        $this->actingAs($this->superAdmin);

        // Create test realtors with different values
        RealtorList::create([
            'full_name' => 'Adam Smith',
            'email' => 'adam@example.com',
            'city' => 'Portland',
            'listings' => 10,
        ]);

        RealtorList::create([
            'full_name' => 'Zoe Johnson',
            'email' => 'zoe@example.com',
            'city' => 'Eugene',
            'listings' => 5,
        ]);

        RealtorList::create([
            'full_name' => 'Bob Wilson',
            'email' => 'bob@example.com',
            'city' => 'Salem',
            'listings' => 15,
        ]);

        // Test default sorting (full_name asc)
        $component = Livewire::test(Index::class);
        $realtors = $component->viewData('realtors');
        $this->assertEquals('Adam Smith', $realtors->first()->full_name);

        // Test sorting by full_name desc
        $component->call('sortBy', 'full_name')
            ->assertSet('sortField', 'full_name')
            ->assertSet('sortDirection', 'desc');

        $realtors = $component->viewData('realtors');
        $this->assertEquals('Zoe Johnson', $realtors->first()->full_name);

        // Test sorting by city
        $component->call('sortBy', 'city')
            ->assertSet('sortField', 'city')
            ->assertSet('sortDirection', 'asc');

        // Test sorting by listings (numeric)
        $component->call('sortBy', 'listings')
            ->assertSet('sortField', 'listings')
            ->assertSet('sortDirection', 'asc');

        $realtors = $component->viewData('realtors');
        $this->assertEquals(5, $realtors->first()->listings); // Zoe has 5 listings
    }

    /**
     * Test column selection functionality
     */
    public function test_column_selection_functionality()
    {
        $this->actingAs($this->superAdmin);

        $component = Livewire::test(Index::class)
            ->assertSet('showColumnSelector', false);

        // Test toggle column selector
        $component->call('toggleColumnSelector')
            ->assertSet('showColumnSelector', true);

        // Test default selected columns
        $defaultColumns = [
            'full_name',
            'email',
            'office_name',
            'city',
            'state',
            'phone',
            'license_number',
            'expiration_date',
        ];
        $component->assertSet('selectedColumns', $defaultColumns);

        // Test toggle column off
        $component->call('toggleColumn', 'phone');
        $selectedColumns = $component->get('selectedColumns');
        $this->assertNotContains('phone', $selectedColumns);

        // Test toggle column on
        $component->call('toggleColumn', 'mobile');
        $selectedColumns = $component->get('selectedColumns');
        $this->assertContains('mobile', $selectedColumns);
    }

    /**
     * Test session storage for column preferences
     */
    public function test_session_storage_for_column_preferences()
    {
        $this->actingAs($this->superAdmin);

        // Set custom columns in session
        $customColumns = ['full_name', 'email', 'city', 'mobile'];
        session(['realtor_columns' => $customColumns]);

        $component = Livewire::test(Index::class);
        $this->assertEquals($customColumns, $component->get('selectedColumns'));

        // Test that toggling column updates session
        $component->call('toggleColumn', 'phone');
        $this->assertContains('phone', session('realtor_columns'));
    }

    /**
     * Test pagination
     */
    public function test_pagination()
    {
        $this->actingAs($this->superAdmin);

        // Create 150 realtors to test pagination (page size is 100)
        for ($i = 1; $i <= 150; $i++) {
            RealtorList::create([
                'email' => "realtor{$i}@example.com",
                'full_name' => "Realtor {$i}",
                'city' => 'Portland',
                'state' => 'OR',
            ]);
        }

        $component = Livewire::test(Index::class);
        $realtors = $component->viewData('realtors');

        // Test first page shows 100 items
        $this->assertEquals(100, $realtors->count());
        $this->assertEquals(150, $realtors->total());
        $this->assertTrue($realtors->hasPages());

        // Test pagination navigation
        $component->call('gotoPage', 2);
        $realtors = $component->viewData('realtors');
        $this->assertEquals(50, $realtors->count()); // Remaining 50 on page 2
    }

    /**
     * Test search resets pagination
     */
    public function test_search_resets_pagination()
    {
        $this->actingAs($this->superAdmin);

        // Create enough realtors for pagination
        for ($i = 1; $i <= 150; $i++) {
            RealtorList::create([
                'email' => "realtor{$i}@example.com",
                'full_name' => "Realtor {$i}",
                'city' => $i <= 10 ? 'Portland' : 'Eugene',
                'state' => 'OR',
            ]);
        }

        $component = Livewire::test(Index::class);

        // Go to page 2
        $component->call('gotoPage', 2);

        // Search should reset to page 1
        $component->set('search', 'Portland');
        $realtors = $component->viewData('realtors');
        $this->assertEquals(1, $realtors->currentPage());
        $this->assertEquals(10, $realtors->total()); // Only 10 Portland realtors
    }

    /**
     * Test export functionality redirects correctly
     */
    public function test_export_functionality()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(Index::class)
            ->call('export')
            ->assertRedirect(route('realtors.export'));
    }

    /**
     * Test empty state
     */
    public function test_empty_state()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(Index::class)
            ->assertSee('No realtors found')
            ->assertSee('Import realtor data using the CSV import command');
    }

    /**
     * Test empty state with search
     */
    public function test_empty_state_with_search()
    {
        $this->actingAs($this->superAdmin);

        // Create a realtor
        RealtorList::create([
            'email' => 'test@example.com',
            'full_name' => 'Test Realtor',
            'city' => 'Portland',
            'state' => 'OR',
        ]);

        Livewire::test(Index::class)
            ->set('search', 'nonexistent')
            ->assertSee('No realtors found')
            ->assertSee('Try adjusting your search criteria');
    }

    /**
     * Test query string parameters
     */
    public function test_query_string_parameters()
    {
        $this->actingAs($this->superAdmin);

        $component = Livewire::test(Index::class)
            ->set('search', 'test search')
            ->set('sortField', 'city')
            ->set('sortDirection', 'desc');

        // Test that query string is updated
        $component->assertSet('search', 'test search')
            ->assertSet('sortField', 'city')
            ->assertSet('sortDirection', 'desc');
    }

    /**
     * Test component handles large datasets efficiently
     */
    public function test_handles_large_datasets()
    {
        $this->actingAs($this->superAdmin);

        // Create 500 realtors
        $realtors = [];
        for ($i = 1; $i <= 500; $i++) {
            $realtors[] = [
                'email' => "realtor{$i}@example.com",
                'full_name' => "Realtor {$i}",
                'city' => 'Portland',
                'state' => 'OR',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        RealtorList::insert($realtors);

        // Test component loads without timeout
        $start = microtime(true);
        $component = Livewire::test(Index::class);
        $end = microtime(true);

        $this->assertLessThan(5, $end - $start, 'Component should load within 5 seconds');

        $realtorsData = $component->viewData('realtors');
        $this->assertEquals(100, $realtorsData->count()); // Shows 100 per page
        $this->assertEquals(500, $realtorsData->total());
    }
}
