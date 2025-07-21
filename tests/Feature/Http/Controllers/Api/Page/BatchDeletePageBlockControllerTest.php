<?php

namespace Tests\Feature\Api\Page;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchDeletePageBlockControllerTest extends TestCase
{
    use RefreshDatabase;

    
    public function it_can_batch_delete_page_blocks()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $this->actingAs($user)
            ->json('POST', route('api.pages.batch-delete-block', ['page' => $page->id]), [
                'type' => 'text'
            ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Page block deleted',
            ]);
    }

    
    public function it_returns_error_if_delete_fails()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        // Mock the PageService to return false (indicating failure)
        $this->mock(\App\Services\Page\PageService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('deletePageBlocksByType')->andReturn(false);
        });

        $this->actingAs($user)
            ->json('POST', route('api.pages.batch-delete-block', ['page' => $page->id]), [
                'type' => 'text'
            ])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Error deleting page blocks',
            ]);
    }

    
    public function it_requires_authentication()
    {
        $page = Page::factory()->create();

        $this->json('POST', route('api.pages.batch-delete-block', ['page' => $page->id]), [
            'type' => 'text'
        ])
        ->assertStatus(401); // Or a redirect, depending on configuration
    }


    
    public function it_validates_the_request()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $this->actingAs($user)
            ->json('POST', route('api.pages.batch-delete-block', ['page' => $page->id]), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}