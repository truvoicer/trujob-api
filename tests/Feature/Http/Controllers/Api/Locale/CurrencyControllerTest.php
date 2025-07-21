<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class CurrencyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->site = Site::factory()->create();
        $this->user->sites()->attach($this->site->id, ['role' => 'admin']);

        Passport::actingAs($this->user);
    }


    public function test_index_returns_collection_of_currencies(): void
    {
        Currency::factory(3)->create();

        $response = $this->getJson(route('currencies.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'code',
                        'symbol',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_show_returns_a_currency(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->getJson(route('currencies.show', $currency));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'code',
                    'symbol',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_store_creates_a_currency(): void
    {
        $data = [
            'name' => $this->faker->currencyName,
            'code' => $this->faker->unique()->currencyCode,
            'symbol' => $this->faker->currencySymbol,
        ];

        $response = $this->postJson(route('currencies.store'), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Currency created',
            ]);

        $this->assertDatabaseHas('currencies', $data);
    }

    public function test_store_validation_failure(): void
    {
        $data = [
            'name' => '',
            'code' => '',
            'symbol' => '',
        ];

        $response = $this->postJson(route('currencies.store'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'code', 'symbol']);
    }


    public function test_update_updates_a_currency(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'name' => 'Updated Currency',
            'code' => 'UPD',
            'symbol' => '$',
        ];

        $response = $this->putJson(route('currencies.update', $currency), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Currency updated',
            ]);

        $this->assertDatabaseHas('currencies', $data);
    }

    public function test_update_validation_failure(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'name' => '',
            'code' => '',
            'symbol' => '',
        ];

        $response = $this->putJson(route('currencies.update', $currency), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'code', 'symbol']);
    }

    public function test_destroy_deletes_a_currency(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->deleteJson(route('currencies.destroy', $currency));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Currency deleted',
            ]);

        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    public function test_destroy_currency_deletion_failure(): void
    {
        // Mock the CurrencyService to return false for deleteCurrency
        $this->mock(\App\Services\Locale\CurrencyService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('deleteCurrency')->andReturn(false);
        });

        $currency = Currency::factory()->create();

        $response = $this->deleteJson(route('currencies.destroy', $currency));

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error deleting currency',
            ]);
    }

}