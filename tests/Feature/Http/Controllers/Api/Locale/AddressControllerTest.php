<?php

namespace Tests\Feature\Http\Controllers\Api\Locale;

use App\Models\Address;

use App\Enums\SiteStatus;
use App\Models\Country;
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

class AddressControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected Country $country;

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
        $this->country = Country::factory()->create([
            'name' => 'United States',
            'iso2' => 'US',
            'iso3' => 'USA',
            'phone_code' => '1',
            'is_active' => true,
        ]);
    }

    public function test_it_can_list_addresses(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $address = Address::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson(route('locale.address.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }


    public function test_it_can_show_a_specific_address(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson(route('locale.address.show', $address));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $address->id,
        ]);
    }


    public function test_it_can_create_an_address(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $user = User::factory()->create();
        $addressData = [
            'label' => 'Home Address',
            'address_line_1' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'postal_code' => '12345',
            'type' => 'shipping',
            'country_id' => $this->country->id,
        ];

        $response = $this->postJson(route('locale.address.store'), $addressData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('addresses', $addressData);
    }


    public function test_it_can_update_an_address(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);
        $updatedAddressData = [
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ];

        $response = $this->patchJson(route('locale.address.update', $address), $updatedAddressData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ]);
    }


    public function test_it_cannot_update_an_address_that_does_not_belong_to_the_user(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'country_id' => $this->country->id,
        ]);
        $updatedAddressData = [
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ];

        $response = $this->patchJson(route('locale.address.update', $address), $updatedAddressData);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ]);
    }


    public function test_it_can_delete_an_address(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->deleteJson(route('locale.address.destroy', $address));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }


    public function test_it_cannot_delete_an_address_that_does_not_belong_to_the_user(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $anotherUser->id, 'country_id' => $this->country->id]);

        $response = $this->deleteJson(route('locale.address.destroy', $address));

        $response->assertStatus(404);
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }
}
