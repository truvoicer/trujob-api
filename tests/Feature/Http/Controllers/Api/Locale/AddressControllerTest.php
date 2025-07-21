<?php

namespace Tests\Feature\Api\Locale;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    
    public function it_can_list_addresses()
    {
        $user = User::factory()->create();
        $address = Address::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('addresses.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    
    public function it_can_show_a_specific_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('addresses.show', $address));

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

        $response = $this->actingAs($user)->postJson(route('addresses.store'), $addressData);

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

        $response = $this->actingAs($user)->putJson(route('addresses.update', $address), $updatedAddressData);

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

        $response = $this->actingAs($user)->putJson(route('addresses.update', $address), $updatedAddressData);

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

        $response = $this->actingAs($user)->deleteJson(route('addresses.destroy', $address));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

     
    public function it_cannot_delete_an_address_that_does_not_belong_to_the_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user)->deleteJson(route('addresses.destroy', $address));

        $response->assertStatus(404);
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }
}