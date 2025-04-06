<?php

namespace Tests\Unit;

use App\Models\Agent;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AgentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_agent()
    {
        $account = Account::factory()->create();

        $agentData = [
            'account_id' => $account->id,
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '555-123-4567',
            'address_1' => '123 Main St',
            'address_2' => 'Apt 101',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ];

        $agent = Agent::create($agentData);

        $this->assertInstanceOf(Agent::class, $agent);
        $this->assertEquals('John Smith', $agent->name);
        $this->assertEquals('john.smith@example.com', $agent->email);
        $this->assertEquals('555-123-4567', $agent->phone);
        $this->assertEquals('123 Main St', $agent->address_1);
        $this->assertEquals('Apt 101', $agent->address_2);
        $this->assertEquals('Portland', $agent->city);
        $this->assertEquals('OR', $agent->state);
        $this->assertEquals('97201', $agent->zip);
    }

    #[Test]
    public function it_belongs_to_an_account()
    {
        $account = Account::factory()->create();
        $agent = Agent::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $agent->account);
        $this->assertEquals($account->id, $agent->account->id);
    }

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $expectedFillable = [
            'account_id',
            'name',
            'address_1',
            'address_2',
            'city',
            'state',
            'zip',
            'phone',
            'email',
        ];

        $agent = new Agent();
        $fillable = $agent->getFillable();

        $this->assertEquals(sort($expectedFillable), sort($fillable));
    }

    #[Test]
    public function it_can_update_agent_attributes()
    {
        $agent = Agent::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com'
        ]);

        $agent->update([
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);

        $this->assertEquals('Updated Name', $agent->name);
        $this->assertEquals('updated@example.com', $agent->email);

        // Verify the changes are persisted to the database
        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    #[Test]
    public function it_can_be_deleted()
    {
        $agent = Agent::factory()->create();

        $agentId = $agent->id;

        $agent->delete();

        $this->assertDatabaseMissing('agents', ['id' => $agentId]);
        $this->assertNull(Agent::find($agentId));
    }
}
