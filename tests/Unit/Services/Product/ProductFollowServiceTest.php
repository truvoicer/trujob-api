<?php

namespace Tests\Unit\Services\Product;

use App\Models\Product;
use App\Models\ProductFollow;
use App\Models\User;
use App\Services\Product\ProductFollowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFollowServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductFollowService $productFollowService;
    protected Product $product;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productFollowService = new ProductFollowService();
        $this->product = Product::factory()->create();
        $this->user = User::factory()->create();

        //Mock the site relationship (important for the User lookup in createProductFollow)
        $this->user->site()->associate($this->product->site);
        $this->user->save();

        //Set the site on the ProductFollowService (as would be done in a real app)
        $this->productFollowService->setSite($this->product->site);
    }

    public function testCreateProductFollow(): void
    {
        $result = $this->productFollowService->createProductFollow($this->product, [$this->user->id]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('product_follows', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function testCreateProductFollowThrowsExceptionWhenUserNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->productFollowService->createProductFollow($this->product, [999]); // Non-existent user ID
    }

    public function testUpdateProductFollow(): void
    {
        $productFollow = ProductFollow::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
        ]);

        $data = ['notes' => 'Updated notes'];

        $result = $this->productFollowService->updateProductFollow($productFollow, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('product_follows', [
            'id' => $productFollow->id,
            'notes' => 'Updated notes',
        ]);
    }

    public function testUpdateProductFollowThrowsException(): void
    {
        $productFollow = $this->getMockBuilder(ProductFollow::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productFollow->method('update')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating product follow');

        $this->productFollowService->updateProductFollow($productFollow, []);
    }


    public function testDeleteProductFollow(): void
    {
        $productFollow = ProductFollow::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
        ]);

        $result = $this->productFollowService->deleteProductFollow($productFollow);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('product_follows', [
            'id' => $productFollow->id,
        ]);
    }

    public function testDeleteProductFollowThrowsException(): void
    {
        $productFollow = $this->getMockBuilder(ProductFollow::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productFollow->method('delete')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting product follow');

        $this->productFollowService->deleteProductFollow($productFollow);
    }

    public function testDetachBulkFollowsFromProduct(): void
    {
        $productFollow = ProductFollow::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
        ]);

        $result = $this->productFollowService->detachBulkFollowsFromProduct($this->product, [$this->user->id]);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('product_follows', [
            'id' => $productFollow->id,
        ]);
    }

    public function testDetachBulkFollowsFromProductThrowsExceptionWhenProductFollowNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product follow not found for user id: 999');

        $this->productFollowService->detachBulkFollowsFromProduct($this->product, [999]);
    }

    public function testDetachBulkFollowsFromProductThrowsExceptionWhenDeleteFails(): void
    {
        $productFollow = $this->getMockBuilder(ProductFollow::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productFollow->method('delete')->willReturn(false);
        $productFollow->method('getAttribute')->with('user_id')->willReturn($this->user->id); // Required due to message in original code

        $this->product->method('productFollow')->willReturnSelf();
        $this->product->method('where')->willReturnSelf();
        $this->product->method('first')->willReturn($productFollow);


        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting product follow for user id: ' . $this->user->id);

        $this->productFollowService->detachBulkFollowsFromProduct($this->product, [$this->user->id]);
    }
}
