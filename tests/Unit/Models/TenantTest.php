<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_tenant()
    {
        $account = Account::factory()->create();

        $tenantData = [
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '555-123-4567',
            'address_1' => '123 Main St',
            'address_2' => 'Apt 101',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ];

        $tenant = Tenant::create($tenantData);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertEquals('John', $tenant->first_name);
        $this->assertEquals('Doe', $tenant->last_name);
        $this->assertEquals('john.doe@example.com', $tenant->email);
        $this->assertEquals('555-123-4567', $tenant->phone);
        $this->assertEquals('123 Main St', $tenant->address_1);
        $this->assertEquals('Apt 101', $tenant->address_2);
        $this->assertEquals('Portland', $tenant->city);
        $this->assertEquals('OR', $tenant->state);
        $this->assertEquals('97201', $tenant->zip);
    }

    #[Test]
    public function it_belongs_to_an_account()
    {
        $account = Account::factory()->create();
        $tenant = Tenant::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $tenant->account);
        $this->assertEquals($account->id, $tenant->account->id);
    }

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $expectedFillable = [
            'account_id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'address_1',
            'address_2',
            'city',
            'state',
            'zip',
        ];

        $tenant = new Tenant;
        $fillable = $tenant->getFillable();

        $this->assertEquals(sort($expectedFillable), sort($fillable));
    }

    #[Test]
    public function it_returns_full_name_attribute()
    {
        $tenant = Tenant::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $this->assertEquals('Jane Smith', $tenant->full_name);
    }

    #[Test]
    public function it_returns_full_address_attribute()
    {
        $tenant = Tenant::factory()->create([
            'address_1' => '456 Oak St',
            'address_2' => 'Suite 7B',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97202',
        ]);

        $this->assertEquals('456 Oak St, Suite 7B, Portland, OR 97202', $tenant->full_address);

        // Test without address_2
        $tenant2 = Tenant::factory()->create([
            'address_1' => '789 Pine St',
            'address_2' => null,
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97203',
        ]);

        $this->assertEquals('789 Pine St, Portland, OR 97203', $tenant2->full_address);
    }

    #[Test]
    public function it_returns_validation_rules()
    {
        $rules = Tenant::validationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('first_name', $rules);
        $this->assertArrayHasKey('last_name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('phone', $rules);
        $this->assertArrayHasKey('address_1', $rules);
        $this->assertArrayHasKey('city', $rules);
        $this->assertArrayHasKey('state', $rules);
        $this->assertArrayHasKey('zip', $rules);
    }

    #[Test]
    public function it_returns_validation_messages()
    {
        $messages = Tenant::messages();

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('zip.regex', $messages);
        $this->assertArrayHasKey('state.size', $messages);
    }

    #[Test]
    public function it_can_update_tenant_attributes()
    {
        $tenant = Tenant::factory()->create([
            'first_name' => 'Original',
            'last_name' => 'Name',
            'email' => 'original@example.com',
        ]);

        $tenant->update([
            'first_name' => 'Updated',
            'last_name' => 'Person',
            'email' => 'updated@example.com',
        ]);

        $this->assertEquals('Updated', $tenant->first_name);
        $this->assertEquals('Person', $tenant->last_name);
        $this->assertEquals('updated@example.com', $tenant->email);

        // Verify changes are persisted to the database
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'first_name' => 'Updated',
            'last_name' => 'Person',
            'email' => 'updated@example.com',
        ]);
    }

    #[Test]
    public function it_can_be_deleted()
    {
        $tenant = Tenant::factory()->create();

        $tenantId = $tenant->id;

        $tenant->delete();

        $this->assertDatabaseMissing('tenants', ['id' => $tenantId]);
        $this->assertNull(Tenant::find($tenantId));
    }
}
