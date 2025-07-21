<?php

namespace Tests\Feature\Api\Pagination;

use App\Enums\Pagination\PaginationScrollType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationScrollTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_json_response_with_pagination_scroll_types(): void
    {
        $response = $this->getJson(route('api.pagination-scroll-type'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'value'
                    ],
                ],
            ]);

        $expectedData = collect(PaginationScrollType::cases())
            ->map(fn (PaginationScrollType $type) => [
                'name' => $type->name,
                'value' => $type->value,
            ])
            ->toArray();

        $response->assertJson(['data' => $expectedData]);
    }
    /** @test */
    public function it_returns_the_correct_data_types_in_the_response(): void
    {
        $response = $this->getJson(route('api.pagination-scroll-type'));

        $response->assertOk();
        $responseData = $response->json('data');

        foreach ($responseData as $item) {
            $this->assertIsString($item['name']);
            $this->assertIsString($item['value']);
        }
    }
}