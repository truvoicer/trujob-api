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
use App\Services\Locale\CountryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class BulkCountryControllerTest extends TestCase
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
        $this->user->roles()->attach(Role::factory()->create([
            'name' => 'superuser'
        ])->id);

        $this->siteUser = SiteUser::create([
            'user_id' => $this->user->id,
            'site_id' => $this->site->id,
            'status' => SiteStatus::ACTIVE->value,
        ]);

    }

    public function test_store_creates_country_batch_successfully(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        // Arrange
        $data = [
            'countries' => [
                [
                    'name' => 'Test Country 1',
                    'iso2' => 'TC1',
                    'iso3' => 'TCY',
                    'phone_code' => '123',
                ],
                [
                    'name' => 'Test Country 2',
                    'iso2' => 'TC2',
                    'iso3' => 'TCZ',
                    'phone_code' => '456',
                ],
            ],
        ];

        $this->mock(CountryService::class, function (MockInterface $mock) use ($data) {
            $mock->shouldReceive('setUser')->once()->with($this->siteUser->user);
            $mock->shouldReceive('setSite')->once()->with($this->siteUser->site);
            $mock->shouldReceive('createCountryBatch')->once()->with($data)->andReturn(true);
        });


        // Act
        $response = $this->postJson(route('locale.country.bulk.store'), $data);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Country batch created',
            ]);
    }

    public function test_store_returns_error_if_country_batch_creation_fails(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        // Arrange
        $data = [
            'countries' => [
                [
                    'name' => 'Test Country 1',
                    'iso2' => 'TC1',
                    'iso3' => 'TCY',
                    'phone_code' => '123',
                ],
                [
                    'name' => 'Test Country 2',
                    'iso2' => 'TC2',
                    'iso3' => 'TCZ',
                    'phone_code' => '456',
                ],
            ],
        ];

        $this->mock(CountryService::class, function (MockInterface $mock) use ($data) {
            $mock->shouldReceive('setUser')->once()->with($this->siteUser->user);
            $mock->shouldReceive('setSite')->once()->with($this->siteUser->site);
            $mock->shouldReceive('createCountryBatch')->once()->with($data)->andReturn(false);
        });

        // Act
        $response = $this->postJson(route('locale.country.bulk.store'), $data);

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error creating country batch',
            ]);
    }

    public function test_store_validates_input_data(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        // Arrange
        $data = [
            'countries' => [
                [
                    'name' => '', // Invalid: Required
                    'iso2' => 'TOOLONG', // Invalid: Max 2 chars
                    'iso3' => 'TOOLONG', // Invalid: Max 3 chars
                    'phone_code' => 'abc', // Invalid: Numeric
                ],
            ],
        ];

        // Act
        $response = $this->postJson(route('locale.country.bulk.store'), $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['countries.0.name', 'countries.0.iso2', 'countries.0.iso3', 'countries.0.phone_code']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}