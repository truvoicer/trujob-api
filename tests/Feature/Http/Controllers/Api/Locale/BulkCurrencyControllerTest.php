<?php

namespace Tests\Feature\Api\Locale;

use App\Models\Currency;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BulkCurrencyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
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
        $response = $this->actingAs($user)
            ->postJson(route('bulk-currencies.store'), $data);

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Currency batch created']);

        $this->assertDatabaseHas('currencies', ['name' => 'Test Currency 1', 'code' => 'TC1', 'site_id' => $site->id]);
        $this->assertDatabaseHas('currencies', ['name' => 'Test Currency 2', 'code' => 'TC2', 'site_id' => $site->id]);
    }


    /** @test */
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
        $response = $this->actingAs($user)
            ->postJson(route('bulk-currencies.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
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