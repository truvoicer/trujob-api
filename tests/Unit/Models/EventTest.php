<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Event
     */
    private $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = new Event();
    }

    protected function tearDown(): void
    {
        unset($this->event);

        parent::tearDown();
    }

    
    public function test_it_has_comments_relationship()
    {
        $this->assertInstanceOf(MorphMany::class, $this->event->comments());
    }

    
    public function test_it_returns_correct_morph_many_relation_for_comments()
    {
        $relation = $this->event->comments();

        $this->assertEquals('ticketable', $relation->getMorphType());
        $this->assertEquals('ticketable_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
        $this->assertEquals(Ticket::class, $relation->getModel());
    }

    
    public function test_it_can_have_comments()
    {
        $event = Event::factory()->create();
        $ticket1 = Ticket::factory()->create(['ticketable_id' => $event->id, 'ticketable_type' => Event::class]);
        $ticket2 = Ticket::factory()->create(['ticketable_id' => $event->id, 'ticketable_type' => Event::class]);

        $this->assertCount(2, $event->comments);
        $this->assertTrue($event->comments->contains($ticket1));
        $this->assertTrue($event->comments->contains($ticket2));
    }
}
