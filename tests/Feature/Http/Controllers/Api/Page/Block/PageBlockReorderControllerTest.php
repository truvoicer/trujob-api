<?php

namespace Tests\Feature\Api\Page\Block;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBlockReorderControllerTest extends TestCase
{
    use RefreshDatabase;

    
    public function it_can_reorder_a_page_block(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock1 = PageBlock::factory()->create(['page_id' => $page->id, 'order' => 1]);
        $pageBlock2 = PageBlock::factory()->create(['page_id' => $page->id, 'order' => 2]);


        $response = $this->actingAs($user)
            ->postJson(route('api.pages.blocks.reorder', ['page' => $page->id, 'page_block' => $pageBlock1->id]), [
                'direction' => 'down',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page block moved down',
            ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock1->id,
            'order' => 2,
        ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock2->id,
            'order' => 1,
        ]);


    }

    
    public function it_requires_a_valid_direction(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this->actingAs($user)
            ->postJson(route('api.pages.blocks.reorder', ['page' => $page->id, 'page_block' => $pageBlock->id]), [
                'direction' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }

    
    public function it_returns_403_if_user_does_not_have_permission(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $otherUser->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this->actingAs($user)
            ->postJson(route('api.pages.blocks.reorder', ['page' => $page->id, 'page_block' => $pageBlock->id]), [
                'direction' => 'down',
            ]);

        $response->assertStatus(403);
    }


}