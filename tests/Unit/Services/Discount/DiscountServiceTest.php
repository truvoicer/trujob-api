<?php

namespace Tests\Unit\Services\Discount;

use App\Enums\Order\Discount\DiscountableType;
use App\Models\Discount;
use App\Models\DiscountDefault;
use App\Models\Product;
use App\Services\Discount\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DiscountService $discountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountService = new DiscountService();
    }

    /** @test */
    public function it_can_create_a_discount()
    {
        $data = [
            'label' => 'Test Discount',
            'value' => 10,
            'type' => 'percentage',
            'discountables' => [
                [
                    'discountable_type' => DiscountableType::PRODUCT->value,
                    'discountable_id' => Product::factory()->create()->id,
                ],
            ],
            'is_default' => true,
        ];

        $this->assertTrue($this->discountService->createDiscount($data));

        $this->assertDatabaseHas('discounts', [
            'label' => 'Test Discount',
            'value' => 10,
            'type' => 'percentage',
        ]);

        $discount = Discount::where('label', 'Test Discount')->first();
        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_type' => DiscountableType::PRODUCT->value,
        ]);

        $this->assertDatabaseHas('discount_defaults', ['discount_id' => $discount->id]);
    }

     /** @test */
    public function it_creates_a_discount_with_a_slugified_name_if_name_is_empty()
    {
        $data = [
            'label' => 'Test Discount',
            'value' => 10,
            'type' => 'percentage',
        ];

        $this->assertTrue($this->discountService->createDiscount($data));

        $discount = Discount::where('label', 'Test Discount')->first();

        $this->assertEquals(Str::slug('Test Discount'), $discount->name);
    }

    /** @test */
    public function it_can_update_a_discount()
    {
        $discount = Discount::factory()->create();
        $data = [
            'label' => 'Updated Discount',
            'value' => 20,
            'type' => 'fixed',
            'discountables' => [
                [
                    'discountable_type' => DiscountableType::PRODUCT->value,
                    'discountable_id' => Product::factory()->create()->id,
                ],
            ],
            'is_default' => true,
        ];

        $this->assertTrue($this->discountService->updateDiscount($discount, $data));

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'label' => 'Updated Discount',
            'value' => 20,
            'type' => 'fixed',
        ]);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_type' => DiscountableType::PRODUCT->value,
        ]);

        $this->assertDatabaseHas('discount_defaults', ['discount_id' => $discount->id]);
    }

    /** @test */
    public function it_can_sync_discountables()
    {
        $discount = Discount::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $data = [
            [
                'discountable_type' => DiscountableType::PRODUCT->value,
                'discountable_id' => $product1->id,
            ],
            [
                'discountable_type' => DiscountableType::PRODUCT->value,
                'discountable_id' => $product2->id,
            ],
        ];

        $this->discountService->syncDiscountables($discount, $data);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_type' => DiscountableType::PRODUCT->value,
            'discountable_id' => $product1->id,
        ]);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_type' => DiscountableType::PRODUCT->value,
            'discountable_id' => $product2->id,
        ]);
    }

    /** @test */
    public function it_can_set_a_discount_as_default()
    {
        $discount = Discount::factory()->create();

        $this->discountService->setAsDefault($discount);

        $this->assertDatabaseHas('discount_defaults', ['discount_id' => $discount->id]);
    }

    /** @test */
    public function it_does_nothing_when_setting_as_default_if_already_default()
    {
        $discount = Discount::factory()->create();
        DiscountDefault::create(['discount_id' => $discount->id]);

        $this->discountService->setAsDefault($discount);

        $this->assertCount(1, DiscountDefault::where('discount_id', $discount->id)->get());
    }

    /** @test */
    public function it_can_remove_a_discount_as_default()
    {
        $discount = Discount::factory()->create();
        DiscountDefault::create(['discount_id' => $discount->id]);

        $this->discountService->removeAsDefault($discount);

        $this->assertDatabaseMissing('discount_defaults', ['discount_id' => $discount->id]);
    }

    /** @test */
    public function it_can_delete_a_discount()
    {
        $discount = Discount::factory()->create();

        $this->assertTrue($this->discountService->deleteDiscount($discount));

        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);
    }
}