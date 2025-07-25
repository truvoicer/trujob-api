<?php

namespace Tests\Unit\Models;

use App\Models\PetProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var PetProduct
     */
    private PetProduct $petProduct;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        // Create a new PetProduct instance for each test.  This can be adjusted if needed based on your actual tests.
        $this->petProduct = PetProduct::factory()->create([
            'created_by_user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->petProduct);

        parent::tearDown();
    }

    /**
     * Test that a PetProduct instance can be created.
     *
     * @return void
     */
    public function testPetProductCanBeCreated(): void
    {
        $this->assertInstanceOf(PetProduct::class, $this->petProduct);
    }

    /**
     * Test that a PetProduct can be saved to the database.
     *
     * @return void
     */
    public function testPetProductCanBeSaved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id
        ]);
        $petProduct = PetProduct::create([
            'created_by_user_id' => $user->id,
            'product_id' => $product->id
        ]);


        $this->assertDatabaseHas('pet_products', ['id' => $petProduct->id]);
    }

    /**
     * Test that a PetProduct can be retrieved from the database.
     *
     * @return void
     */
    public function testPetProductCanBeRetrieved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id
        ]);
        $petProduct = PetProduct::create([
            'created_by_user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $retrievedPetProduct = PetProduct::find($petProduct->id);

        $this->assertInstanceOf(PetProduct::class, $retrievedPetProduct);
        $this->assertEquals($petProduct->id, $retrievedPetProduct->id);
    }
}
