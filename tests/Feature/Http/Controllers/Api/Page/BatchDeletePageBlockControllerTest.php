<?php

namespace Tests\Feature\Api\Page;

use App\Models\Page;

use App\Enums\SiteStatus;
use App\Models\Block;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchDeletePageBlockControllerTest extends TestCase
{
    use RefreshDatabase;


    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
        $this->site = Site::factory()->create();
        $this->user = User::factory()->create();
        $this->user->roles()->attach(Role::factory()->create(['name' => 'superuser'])->id);
        $this->siteUser = SiteUser::create([
            'user_id' => $this->user->id,
            'site_id' => $this->site->id,
            'status' => SiteStatus::ACTIVE->value,
        ]);
    }

    public function test_it_can_batch_delete_page_blocks()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $page = Page::factory()
        ->has(
            Block::factory()->count(3),
        )
        ->create([
            'site_id' => $this->site->id,
        ]);

        $this
            ->deleteJson(route('page.block.bulk.destroy', ['page' => $page->id]), [
                'ids' => [1,2]
            ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Page blocks deleted',
            ]);
    }


    public function test_it_returns_error_if_delete_fails()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $page = Page::factory()
        ->has(
            Block::factory()->count(3),
        )
        ->create([
            'site_id' => $this->site->id,
        ]);

        // Mock the PageService to return false (indicating failure)
        $this->mock(\App\Services\Page\PageService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('deletePageBlocksByIds')->andReturn(false);
        });

        $this
            ->deleteJson(route('page.block.bulk.destroy', ['page' => $page->id]), [
                'ids' => [1]
            ])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Error deleting page blocks',
            ]);
    }


    public function test_it_requires_authentication()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $this->deleteJson(route('page.block.bulk.destroy', ['page' => $page->id]))
        ->assertStatus(401); // Or a redirect, depending on configuration
    }



    public function test_it_validates_the_request()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $this
            ->deleteJson(route('page.block.bulk.destroy', ['page' => $page->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);
    }
}
