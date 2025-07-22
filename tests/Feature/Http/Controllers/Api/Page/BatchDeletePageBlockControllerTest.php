<?php

namespace Tests\Feature\Api\Page;

use App\Models\Page;

use App\Enums\SiteStatus;
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
        Sanctum::actingAs($this->siteUser, ['*']);
    }
    
    public function it_can_batch_delete_page_blocks()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $this
            ->json('POST', route('page.batch-delete-block', ['page' => $page->id]), [
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
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        // Mock the PageService to return false (indicating failure)
        $this->mock(\App\Services\Page\PageService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('deletePageBlocksByType')->andReturn(false);
        });

        $this
            ->json('POST', route('page.batch-delete-block', ['page' => $page->id]), [
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
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $this->json('POST', route('page.batch-delete-block', ['page' => $page->id]), [
            'type' => 'text'
        ])
        ->assertStatus(401); // Or a redirect, depending on configuration
    }


    
    public function it_validates_the_request()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $this
            ->json('POST', route('page.batch-delete-block', ['page' => $page->id]), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}