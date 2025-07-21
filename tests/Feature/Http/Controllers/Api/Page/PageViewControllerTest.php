<?php

namespace Tests\Feature\Api\Page;

use App\Enums\ViewType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_index_data()
    {
        $response = $this->getJson(route('page-view.index'));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page view controller index method',
                'data' => collect(ViewType::cases())->pluck('value')->toArray(),
            ]);
    }
}