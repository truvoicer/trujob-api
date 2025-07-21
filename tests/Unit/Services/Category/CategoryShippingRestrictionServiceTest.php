<?php

namespace Tests\Unit\Services\Category;

use App\Enums\MorphEntity;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction;
use App\Repositories\CategoryRepository;
use App\Services\Category\CategoryShippingRestrictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CategoryShippingRestrictionServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryShippingRestrictionService $categoryShippingRestrictionService;
    private CategoryRepository $categoryRepository;
    private Category $category;
    private ShippingMethod $shippingMethod;
    private ShippingRestriction $shippingRestriction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->mock(CategoryRepository::class);
        $this->categoryShippingRestrictionService = new CategoryShippingRestrictionService(
            $this->categoryRepository
        );

        // Seed data
        $this->category = Category::factory()->create();
        $this->shippingMethod = ShippingMethod::factory()->create();
        $this->shippingRestriction = ShippingRestriction::factory()->create([
            'restrictionable_id' => $this->category->id,
            'restrictionable_type' => MorphEntity::CATEGORY->value,
            'shipping_method_id' => $this->shippingMethod->id
        ]);
    }

    public function testValidateRequestPassesWithValidData(): void
    {
        $requestData = ['restriction_id' => $this->category->id];
        $request = new Request($requestData);
        app()->instance('request', $request);

        $this->assertTrue($this->categoryShippingRestrictionService->validateRequest());
    }

    public function testValidateRequestFailsWithInvalidData(): void
    {
        $requestData = ['restriction_id' => 999]; //Non-existent category id
        $request = new Request($requestData);
        app()->instance('request', $request);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->categoryShippingRestrictionService->validateRequest();

    }

    public function testStoreShippingRestrictionCreatesRestrictionSuccessfully(): void
    {
        $data = ['restriction_id' => $this->category->id];
        $this->categoryRepository->shouldReceive('findById')
            ->once()
            ->with($this->category->id)
            ->andReturn($this->category);

        $restriction = $this->categoryShippingRestrictionService->storeShippingRestriction($this->shippingMethod, $data);

        $this->assertInstanceOf(ShippingRestriction::class, $restriction);
        $this->assertEquals($this->shippingMethod->id, $restriction->shipping_method_id);
        $this->assertEquals($this->category->id, $restriction->restrictionable_id);
        $this->assertEquals(MorphEntity::CATEGORY->value, $restriction->restrictionable_type);
    }

    public function testStoreShippingRestrictionThrowsExceptionWhenCategoryNotFound(): void
    {
        $data = ['restriction_id' => 999]; // Non-existent category id
        $this->categoryRepository->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');

        $this->categoryShippingRestrictionService->storeShippingRestriction($this->shippingMethod, $data);
    }

    public function testUpdateShippingRestrictionUpdatesRestrictionSuccessfully(): void
    {
        $data = ['action' => 'exclude'];

        $updatedRestriction = $this->categoryShippingRestrictionService->updateShippingRestriction($this->shippingRestriction, $data);

        $this->assertInstanceOf(ShippingRestriction::class, $updatedRestriction);
        $this->assertEquals('exclude', $updatedRestriction->action);
    }

    public function testUpdateShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $this->shippingRestriction->action = 'exclude';
        $this->shippingRestriction->save();

        $data = ['action' => 'exclude']; // No change, update fails
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating shipping restriction');

        $this->categoryShippingRestrictionService->updateShippingRestriction($this->shippingRestriction, $data);
    }

    public function testDeleteShippingRestrictionDeletesRestrictionSuccessfully(): void
    {
        $this->assertTrue($this->categoryShippingRestrictionService->deleteShippingRestriction($this->shippingRestriction));
        $this->assertDatabaseMissing('shipping_restrictions', ['id' => $this->shippingRestriction->id]);
    }

    public function testDeleteShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $this->shippingRestriction->id = 999;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting shipping restriction');

        $this->categoryShippingRestrictionService->deleteShippingRestriction($this->shippingRestriction);
    }

    public function testGetRestrictionableEntityResourceDataReturnsCorrectData(): void
    {
        $mockResource = $this->mock(JsonResource::class);
        $mockResource->restrictionable = $this->category;

        $expectedResult = [
            'category' => new CategoryResource($this->category)
        ];

        $this->assertEquals(
            $expectedResult,
            $this->categoryShippingRestrictionService->getRestrictionableEntityResourceData($mockResource)
        );
    }
}