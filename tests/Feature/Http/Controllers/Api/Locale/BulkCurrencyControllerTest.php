<?php

namespace Tests\Feature\Api\Locale;


use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BulkCurrencyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
        $this->site = Site::factory()->create();
        $this->user = User::factory()->create();
        $this->user->roles()->attach(Role::factory()->create(['name' => 'superuser'])->id);
        $this->siteUser = SiteUser::create([
            'user_id' => $this->user->id,
            'site_id' => $this->site->id,
            'status' => SiteStatus::ACTIVE->value,
        ]);
        Sanctum::actingAs($this->siteUser, ['*']);
    }
    
    public function it_can_store_a_bulk_currency(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $data = [
            [
                'name' => 'Test Currency 1',
                'code' => 'TC1',
                'symbol' => '$',
            ],
            [
                'name' => 'Test Currency 2',
                'code' => 'TC2',
                'symbol' => '€',
            ],
        ];

        // Act
        $response = $this
            ->postJson(route('bulk-currencies.store'), $data);

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Currency batch created']);

        $this->assertDatabaseHas('currencies', ['name' => 'Test Currency 1', 'code' => 'TC1', 'site_id' => $site->id]);
        $this->assertDatabaseHas('currencies', ['name' => 'Test Currency 2', 'code' => 'TC2', 'site_id' => $site->id]);
    }


    
    public function it_returns_unprocessable_entity_if_currency_creation_fails(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        // Simulating a failure by sending invalid data
        $data = [
            [
                'name' => null, // Invalid data
                'code' => 'TC1',
                'symbol' => '$',
            ],
        ];

        // Act
        $response = $this
            ->postJson(route('bulk-currencies.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    
    public function it_requires_authentication_to_store_a_bulk_currency(): void
    {
        // Arrange
        $data = [
            [
                'name' => 'Test Currency 1',
                'code' => 'TC1',
                'symbol' => '$',
            ],
        ];

        // Act
        $response = $this->postJson(route('bulk-currencies.store'), $data);

        // Assert
        $response->assertStatus(401);
    }
}