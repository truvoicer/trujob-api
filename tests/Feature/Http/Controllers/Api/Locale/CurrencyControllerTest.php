<?php

namespace Tests\Feature;

use App\Models\Currency;

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
use Laravel\Passport\Passport;

class CurrencyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
        $this->site = Site::factory()->create();
        $this->user = User::factory()->create();
        $this->user->roles()->attach(Role::factory()->create([
            'name' => 'superuser'
        ])->id);

        $this->siteUser = SiteUser::create([
            'user_id' => $this->user->id,
            'site_id' => $this->site->id,
            'status' => SiteStatus::ACTIVE->value,
        ]);

        $this->currency = Currency::factory()->create([
            'code' => 'GBP',
            'name' => 'British Pound',
            'symbol' => '£',
            'is_active' => true,
        ]);
    }


    public function test_index_returns_collection_of_currencies(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        Currency::factory(3)->create();

        $response = $this->getJson(route('locale.currency.index'));

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
        Sanctum::actingAs($this->siteUser, ['*']);
        $currency = Currency::factory()->create();

        $response = $this->getJson(route('locale.currency.show', $currency));

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
        Sanctum::actingAs($this->siteUser, ['*']);
        $data = [
            'name' => $this->faker->name,
            'name_plural' => $this->faker->word,
            'is_active' => true,
            'code' => $this->faker->unique()->currencyCode,
            'symbol' => $this->faker->randomLetter(),
        ];

        $response = $this->postJson(route('locale.currency.store'), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Currency created',
            ]);

        $this->assertDatabaseHas('currencies', $data);
    }

    public function test_store_validation_failure(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $data = [
            'name' => '',
            'code' => '',
            'symbol' => '',
        ];

        $response = $this->postJson(route('locale.currency.store'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'code', 'symbol']);
    }


    public function test_update_updates_a_currency(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $currency = Currency::factory()->create();

        $data = [
            'name' => 'Updated Currency',
            'code' => 'UPD',
            'symbol' => '$',
        ];

        $response = $this->patchJson(route('locale.currency.update', $currency), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Currency updated',
            ]);

        $this->assertDatabaseHas('currencies', $data);
    }

    public function test_update_validation_failure(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $currency = Currency::factory()->create();

        $data = [
            'name' => '',
            'code' => '',
            'symbol' => '',
        ];

        $response = $this->patchJson(route('locale.currency.update', $currency), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'code', 'symbol']);
    }

    public function test_destroy_deletes_a_currency(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $currency = Currency::factory()->create();

        $response = $this->deleteJson(route('locale.currency.destroy', $currency));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Currency deleted',
            ]);

        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    public function test_destroy_currency_deletion_failure(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        // Mock the CurrencyService to return false for deleteCurrency
        $this->mock(\App\Services\Locale\CurrencyService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('deleteCurrency')->andReturn(false);
        });

        $currency = Currency::factory()->create();

        $response = $this->deleteJson(route('locale.currency.destroy', $currency));
        
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error deleting currency',
            ]);
    }

}