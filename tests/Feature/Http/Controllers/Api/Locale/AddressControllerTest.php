<?php

namespace Tests\Feature\Api\Locale;

use App\Models\Address;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

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
    
    public function it_can_list_addresses()
    {
        $user = User::factory()->create();
        $address = Address::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('addresses.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    
    public function it_can_show_a_specific_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('addresses.show', $address));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $address->id,
        ]);
    }

    
    public function it_can_create_an_address()
    {
        $user = User::factory()->create();
        $addressData = [
            'label' => 'Home Address',
            'address_line_1' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'postal_code' => '12345',
            'country' => 'US',
            'type' => 'shipping',
        ];

        $response = $this->postJson(route('addresses.store'), $addressData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('addresses', $addressData);
    }

    
    public function it_can_update_an_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $updatedAddressData = [
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ];

        $response = $this->putJson(route('addresses.update', $address), $updatedAddressData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ]);
    }

    
    public function it_cannot_update_an_address_that_does_not_belong_to_the_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $anotherUser->id]);
        $updatedAddressData = [
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ];

        $response = $this->putJson(route('addresses.update', $address), $updatedAddressData);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
            'label' => 'New Home Address',
            'address_line_1' => '456 Oak Ave',
        ]);
    }

    
    public function it_can_delete_an_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson(route('addresses.destroy', $address));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

     
    public function it_cannot_delete_an_address_that_does_not_belong_to_the_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->deleteJson(route('addresses.destroy', $address));

        $response->assertStatus(404);
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }
}