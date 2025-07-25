<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\EventProduct;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Event
     */
    private $eventProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventProduct = new EventProduct();
    }

    protected function tearDown(): void
    {
        unset($this->eventProduct);

        parent::tearDown();
    }

    public function testCreatedByUserRelationship(): void
    {
        $relation = $this->eventProduct->createdByUser();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('created_by_user_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    public function testProductRelationship(): void
    {
        $relation = $this->eventProduct->product();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    public function testFillableAttributes(): void
    {
        $fillable = $this->eventProduct->getFillable();

        $expected = [
            'created_by_user_id',
            'product_id',
            'notes',
            'latitude',
            'longitude',
            'start_date',
            'end_date',
            'status',
            'is_public',
            'is_all_day',
            'is_recurring',
        ];

        $this->assertEquals($expected, $fillable);
    }
}
