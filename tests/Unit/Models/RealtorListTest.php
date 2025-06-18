<?php

namespace Tests\Unit\Models;

use App\Models\RealtorList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtorListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test model can be created with all attributes
     */
    public function test_can_create_realtor_with_all_attributes()
    {
        $realtorData = [
            'csv_id' => 123456,
            'email' => 'test@example.com',
            'full_name' => 'John Doe',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'suffix' => 'Jr.',
            'office_name' => 'Test Realty',
            'address1' => '123 Main St',
            'address2' => 'Suite 100',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
            'county' => 'Multnomah',
            'phone' => '503-555-1234',
            'fax' => '503-555-5678',
            'mobile' => '503-555-9012',
            'license_type' => 'Principal Broker',
            'license_number' => '200907057',
            'original_issue_date' => '2020-01-15',
            'expiration_date' => '2026-07-31',
            'association' => 'Portland Association Of Realtors',
            'agency' => 'RE/MAX',
            'listings' => 5,
            'listings_volume' => 1250000.50,
            'sold' => 3,
            'sold_volume' => 750000.25,
            'email_status' => 'ok',
        ];

        $realtor = RealtorList::create($realtorData);

        $this->assertInstanceOf(RealtorList::class, $realtor);
        $this->assertEquals('test@example.com', $realtor->email);
        $this->assertEquals('John Doe', $realtor->full_name);
        $this->assertEquals(123456, $realtor->csv_id);
        $this->assertEquals(5, $realtor->listings);
        $this->assertEquals(1250000.50, $realtor->listings_volume);
        $this->assertDatabaseHas('realtor_list', ['email' => 'test@example.com']);
    }

    /**
     * Test date casting works correctly
     */
    public function test_date_casting()
    {
        $realtor = RealtorList::create([
            'email' => 'date-test@example.com',
            'full_name' => 'Date Test',
            'original_issue_date' => '2020-01-15',
            'expiration_date' => '2026-07-31',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $realtor->original_issue_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $realtor->expiration_date);
        $this->assertEquals('2020-01-15', $realtor->original_issue_date->format('Y-m-d'));
        $this->assertEquals('2026-07-31', $realtor->expiration_date->format('Y-m-d'));
    }

    /**
     * Test numeric casting works correctly
     */
    public function test_numeric_casting()
    {
        $realtor = RealtorList::create([
            'email' => 'numeric-test@example.com',
            'full_name' => 'Numeric Test',
            'csv_id' => '789012',
            'listings' => '10',
            'listings_volume' => '2500000.75',
            'sold' => '8',
            'sold_volume' => '2000000.50',
        ]);

        $this->assertIsInt($realtor->csv_id);
        $this->assertIsInt($realtor->listings);
        $this->assertIsInt($realtor->sold);
        $this->assertIsString($realtor->listings_volume); // Decimal cast returns string
        $this->assertIsString($realtor->sold_volume);
        $this->assertEquals(789012, $realtor->csv_id);
        $this->assertEquals(10, $realtor->listings);
        $this->assertEquals('2500000.75', $realtor->listings_volume);
    }

    /**
     * Test model handles null values correctly
     */
    public function test_handles_null_values()
    {
        $realtor = RealtorList::create([
            'email' => 'null-test@example.com',
            'full_name' => 'Null Test',
            'middle_name' => null,
            'suffix' => null,
            'original_issue_date' => null,
            'listings' => null,
            'listings_volume' => null,
        ]);

        $this->assertNull($realtor->middle_name);
        $this->assertNull($realtor->suffix);
        $this->assertNull($realtor->original_issue_date);
        $this->assertNull($realtor->listings);
        $this->assertNull($realtor->listings_volume);
    }

    /**
     * Test table name is correct
     */
    public function test_table_name()
    {
        $realtor = new RealtorList;
        $this->assertEquals('realtor_list', $realtor->getTable());
    }

    /**
     * Test fillable attributes
     */
    public function test_fillable_attributes()
    {
        $realtor = new RealtorList;
        $fillable = $realtor->getFillable();

        $expectedFillable = [
            'csv_id',
            'email',
            'full_name',
            'first_name',
            'middle_name',
            'last_name',
            'suffix',
            'office_name',
            'address1',
            'address2',
            'city',
            'state',
            'zip',
            'county',
            'phone',
            'fax',
            'mobile',
            'license_type',
            'license_number',
            'original_issue_date',
            'expiration_date',
            'association',
            'agency',
            'listings',
            'listings_volume',
            'sold',
            'sold_volume',
            'email_status',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable, "Attribute {$attribute} should be fillable");
        }
    }

    /**
     * Test model casts
     */
    public function test_model_casts()
    {
        $realtor = new RealtorList;
        $casts = $realtor->getCasts();

        $this->assertEquals('integer', $casts['csv_id']);
        $this->assertEquals('date', $casts['original_issue_date']);
        $this->assertEquals('date', $casts['expiration_date']);
        $this->assertEquals('integer', $casts['listings']);
        $this->assertEquals('decimal:2', $casts['listings_volume']);
        $this->assertEquals('integer', $casts['sold']);
        $this->assertEquals('decimal:2', $casts['sold_volume']);
    }

    /**
     * Test model can be queried and filtered
     */
    public function test_can_query_and_filter()
    {
        // Create test data
        RealtorList::create([
            'email' => 'portland@example.com',
            'full_name' => 'Portland Realtor',
            'city' => 'Portland',
            'state' => 'OR',
            'listings' => 10,
        ]);

        RealtorList::create([
            'email' => 'seattle@example.com',
            'full_name' => 'Seattle Realtor',
            'city' => 'Seattle',
            'state' => 'WA',
            'listings' => 5,
        ]);

        RealtorList::create([
            'email' => 'eugene@example.com',
            'full_name' => 'Eugene Realtor',
            'city' => 'Eugene',
            'state' => 'OR',
            'listings' => 8,
        ]);

        // Test basic query
        $allRealtors = RealtorList::all();
        $this->assertCount(3, $allRealtors);

        // Test filtering by state
        $oregonRealtors = RealtorList::where('state', 'OR')->get();
        $this->assertCount(2, $oregonRealtors);

        // Test filtering by city
        $portlandRealtors = RealtorList::where('city', 'Portland')->get();
        $this->assertCount(1, $portlandRealtors);
        $this->assertEquals('Portland Realtor', $portlandRealtors->first()->full_name);

        // Test ordering
        $sortedByListings = RealtorList::orderBy('listings', 'desc')->get();
        $this->assertEquals('Portland Realtor', $sortedByListings->first()->full_name);
        $this->assertEquals('Seattle Realtor', $sortedByListings->last()->full_name);
    }

    /**
     * Test search functionality that would be used in the Livewire component
     */
    public function test_search_functionality()
    {
        // Create test data
        RealtorList::create([
            'email' => 'john.doe@remax.com',
            'full_name' => 'John Doe',
            'office_name' => 'RE/MAX Properties',
            'city' => 'Portland',
            'phone' => '503-555-1234',
            'license_number' => 'LIC123456',
        ]);

        RealtorList::create([
            'email' => 'jane.smith@coldwell.com',
            'full_name' => 'Jane Smith',
            'office_name' => 'Coldwell Banker',
            'city' => 'Eugene',
            'phone' => '541-555-5678',
            'license_number' => 'LIC789012',
        ]);

        // Test search by name
        $results = RealtorList::where('full_name', 'like', '%John%')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('John Doe', $results->first()->full_name);

        // Test search by email
        $results = RealtorList::where('email', 'like', '%remax%')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('john.doe@remax.com', $results->first()->email);

        // Test search by office
        $results = RealtorList::where('office_name', 'like', '%Coldwell%')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Jane Smith', $results->first()->full_name);

        // Test search by phone
        $results = RealtorList::where('phone', 'like', '%541%')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Jane Smith', $results->first()->full_name);

        // Test multi-field search (like in the component)
        $searchTerm = '%portland%';
        $results = RealtorList::where(function ($q) use ($searchTerm) {
            $q->where('full_name', 'like', $searchTerm)
                ->orWhere('email', 'like', $searchTerm)
                ->orWhere('office_name', 'like', $searchTerm)
                ->orWhere('city', 'like', $searchTerm);
        })->get();
        $this->assertCount(1, $results);
        $this->assertEquals('John Doe', $results->first()->full_name);
    }
}
