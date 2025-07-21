<?php

namespace Tests\Feature\Api\Pagination;

use App\Enums\Pagination\PaginationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_all_pagination_types(): void
    {
        $response = $this->getJson(route('api.pagination-type'));

        $response->assertStatus(200);

        $expectedData = collect(PaginationType::cases())->map(fn ($case) => [
            'name' => $case->name,
            'value' => $case->value,
        ])->toArray();

        $response->assertJson(['data' => $expectedData]);

    }
}