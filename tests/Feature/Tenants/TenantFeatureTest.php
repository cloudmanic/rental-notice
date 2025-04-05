<?php

namespace Tests\Feature\Tenants;

use App\Models\Tenant;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Tenants\Index;
use App\Livewire\Tenants\Create;
use App\Livewire\Tenants\Edit;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TenantFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $account;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account
        $this->account = Account::factory()->create();

        // Create a user associated with the account
        $this->user = User::factory()->create();
        $this->user->accounts()->attach($this->account->id);
    }

    #[Test]
    public function users_can_view_tenants_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('tenants.index'));

        // Assert the response is successful
        $response->assertStatus(200);

        // Check for key elements that would be on the tenants index page
        $response->assertSee('Tenants');
        $response->assertSee('Add Tenant');
    }

    #[Test]
    public function tenants_index_shows_tenants_list()
    {
        $this->actingAs($this->user);

        // Create tenants for the current account
        $tenants = Tenant::factory()->count(3)->create([
            'account_id' => $this->account->id
        ]);

        // Create a tenant for another account that should not be visible
        $otherAccount = Account::factory()->create();
        $otherTenant = Tenant::factory()->create([
            'account_id' => $otherAccount->id,
            'first_name' => 'Other',
            'last_name' => 'Tenant'
        ]);

        // Make a direct request to the page
        $response = $this->get(route('tenants.index'));

        // Check that our tenants are listed
        foreach ($tenants as $tenant) {
            $response->assertSee($tenant->full_name);
        }

        // Check that the other account's tenant is not listed
        $response->assertDontSee($otherTenant->full_name);
    }

    #[Test]
    public function users_can_search_tenants()
    {
        $this->actingAs($this->user);

        // Create tenants with unique names
        $tenantA = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@example.com'
        ]);

        $tenantB = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com'
        ]);

        // Test the Livewire component directly
        Livewire::test(Index::class)
            ->set('search', 'John')
            ->assertSee('John Smith')
            ->assertDontSee('Jane Doe');

        // Test searching by last name
        Livewire::test(Index::class)
            ->set('search', 'Doe')
            ->assertSee('Jane Doe')
            ->assertDontSee('John Smith');

        // Test searching by address
        $tenantC = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Mike',
            'last_name' => 'Johnson',
            'address_1' => '123 Maple Street',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97214'
        ]);

        Livewire::test(Index::class)
            ->set('search', 'Maple')
            ->assertSee('Mike Johnson');
    }

    #[Test]
    public function users_can_sort_tenants()
    {
        $this->actingAs($this->user);

        // Create tenants with specific names for sorting testing
        Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Adam',
            'last_name' => 'Anderson',
        ]);

        Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Zoe',
            'last_name' => 'Zimmerman',
        ]);

        // Visit the page directly to check initial sort order (default is by last name)
        $response = $this->get(route('tenants.index'));
        $content = $response->getContent();

        // Simple check that Adam comes before Zoe in the HTML content
        $adamPosition = strpos($content, 'Adam Anderson');
        $zoePosition = strpos($content, 'Zoe Zimmerman');

        $this->assertNotFalse($adamPosition);
        $this->assertNotFalse($zoePosition);
        $this->assertLessThan($zoePosition, $adamPosition);
    }

    #[Test]
    public function users_can_visit_create_tenant_page()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('tenants.create'));

        $response->assertStatus(200);
        $response->assertSee('Add Tenant');
        $response->assertSee('Create a new tenant record in your account');
    }

    #[Test]
    public function users_can_create_new_tenant()
    {
        $this->actingAs($this->user);

        $tenantData = [
            'first_name' => 'New',
            'last_name' => 'Tenant',
            'email' => 'newtenant@example.com',
            'phone' => '555-123-4567',
            'address_1' => '123 Main St',
            'address_2' => 'Apt 4B',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97205'
        ];

        // Test the successful creation and database assertion
        Livewire::test(Create::class)
            ->set('first_name', $tenantData['first_name'])
            ->set('last_name', $tenantData['last_name'])
            ->set('email', $tenantData['email'])
            ->set('phone', $tenantData['phone'])
            ->set('address_1', $tenantData['address_1'])
            ->set('address_2', $tenantData['address_2'])
            ->set('city', $tenantData['city'])
            ->set('state', $tenantData['state'])
            ->set('zip', $tenantData['zip'])
            ->call('save');

        // Assert the tenant was created in the database
        $this->assertDatabaseHas('tenants', [
            'account_id' => $this->account->id,
            'first_name' => $tenantData['first_name'],
            'last_name' => $tenantData['last_name'],
            'email' => $tenantData['email']
        ]);
    }

    #[Test]
    public function tenant_creation_validates_required_fields()
    {
        $this->actingAs($this->user);

        Livewire::test(Create::class)
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('email', 'invalid-email')
            ->set('phone', '123456')
            ->set('address_1', '')
            ->set('city', '')
            ->set('state', '')
            ->set('zip', 'abc')
            ->call('save')
            ->assertHasErrors([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'email',
                'phone' => 'regex',
                'address_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required'
            ]);
    }

    #[Test]
    public function tenant_zip_code_validation_works()
    {
        $this->actingAs($this->user);

        Livewire::test(Create::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set('address_1', '123 Test St')
            ->set('city', 'Portland')
            ->set('state', 'OR')
            ->set('zip', 'abc')
            ->call('save')
            ->assertHasErrors(['zip']);
    }

    #[Test]
    public function phone_number_is_formatted_correctly()
    {
        $this->actingAs($this->user);

        // Test formatting of phone numbers
        $livewire = Livewire::test(Create::class)
            ->set('phone', '5551234567');

        $this->assertEquals('555-123-4567', $livewire->get('phone'));

        $livewire->set('phone', '1234567890');
        $this->assertEquals('123-456-7890', $livewire->get('phone'));
    }

    #[Test]
    public function zip_code_is_formatted_correctly()
    {
        $this->actingAs($this->user);

        // Test formatting of ZIP codes
        $livewire = Livewire::test(Create::class)
            ->set('zip', '123456789');

        $this->assertEquals('12345-6789', $livewire->get('zip'));
    }

    #[Test]
    public function users_can_visit_edit_tenant_page()
    {
        $this->actingAs($this->user);

        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id
        ]);

        $response = $this->get(route('tenants.edit', $tenant));

        $response->assertStatus(200);
        $response->assertSee('Edit Tenant');
        $response->assertSee($tenant->first_name);
        $response->assertSee($tenant->last_name);
    }

    #[Test]
    public function users_can_update_tenant()
    {
        $this->actingAs($this->user);

        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Original',
            'last_name' => 'Name',
            'email' => 'original@example.com'
        ]);

        // Update the tenant through the Livewire component
        Livewire::test(Edit::class, ['tenant' => $tenant])
            ->set('first_name', 'Updated')
            ->set('last_name', 'Person')
            ->set('email', 'updated@example.com')
            ->call('update');

        // Assert the tenant exists with the updated data
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'first_name' => 'Updated',
            'last_name' => 'Person',
            'email' => 'updated@example.com'
        ]);
    }

    #[Test]
    public function tenant_update_validates_required_fields()
    {
        $this->actingAs($this->user);

        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id
        ]);

        Livewire::test(Edit::class, ['tenant' => $tenant])
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('email', 'invalid-email')
            ->set('address_1', '')
            ->call('update')
            ->assertHasErrors([
                'first_name',
                'last_name',
                'email',
                'address_1'
            ]);
    }

    #[Test]
    public function users_can_delete_tenant()
    {
        $this->actingAs($this->user);

        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id
        ]);

        Livewire::test(Edit::class, ['tenant' => $tenant])
            ->call('confirmDelete')
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        // Assert the tenant was deleted from the database
        $this->assertDatabaseMissing('tenants', [
            'id' => $tenant->id
        ]);
    }

    #[Test]
    public function users_can_cancel_tenant_deletion()
    {
        $this->actingAs($this->user);

        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id
        ]);

        Livewire::test(Edit::class, ['tenant' => $tenant])
            ->call('confirmDelete')
            ->assertSet('showDeleteModal', true)
            ->call('cancelDelete')
            ->assertSet('showDeleteModal', false);

        // Assert the tenant still exists in the database
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id
        ]);
    }

    #[Test]
    public function users_cannot_access_other_accounts_tenants()
    {
        $this->actingAs($this->user);

        // Create a tenant for another account
        $otherAccount = Account::factory()->create();
        $otherUser = User::factory()->create();
        $otherUser->accounts()->attach($otherAccount->id);

        $otherTenant = Tenant::factory()->create([
            'account_id' => $otherAccount->id,
            'first_name' => 'Other',
            'last_name' => 'Account'
        ]);

        // Check that when we view the index page, we don't see the other account's tenants
        $response = $this->get(route('tenants.index'));
        $response->assertDontSee($otherTenant->first_name . ' ' . $otherTenant->last_name);

        // Check that we're only getting our own tenants when using the Livewire component
        Livewire::test(Index::class)
            ->assertDontSee($otherTenant->first_name . ' ' . $otherTenant->last_name);
    }

    #[Test]
    public function pagination_works_for_tenants()
    {
        $this->actingAs($this->user);

        // Create more than the per-page limit of tenants
        Tenant::factory()->count(30)->create([
            'account_id' => $this->account->id
        ]);

        $response = $this->get(route('tenants.index'));

        // Look for pagination controls
        $response->assertSee('Next');

        // On Livewire index component
        Livewire::test(Index::class)
            ->assertViewHas('tenants', function ($tenants) {
                // Default pagination should have 25 items on first page
                return $tenants->count() === 25 && $tenants->total() === 30;
            });
    }
}
