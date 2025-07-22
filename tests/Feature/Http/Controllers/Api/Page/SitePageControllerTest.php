<?php

namespace Tests\Feature\Api\Page;

use App\Enums\SiteStatus;
use App\Models\Page;
use App\Models\Role;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Page\PageService;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;

class SitePageControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
    }

    public function test_show_returns_page_resource_on_success(): void
    {

        Sanctum::actingAs($this->site, ['*']);

        $page = Page::factory()->create([
            'site_id' => $this->site->id,
            'permalink' => 'test-page',
        ]);

        $page->load(['roles', 'pageBlocks', 'sidebars']);

        // Act
        $response = $this->getJson(route('site.page.view', ['permalink' => 'test-page']));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                "id",
                "view",
                "permalink",
                "name",
                "title",
                "content",
                "blocks",
                "has_sidebar",
                "sidebars",
                "is_active",
                "is_home",
                "is_featured",
                "is_protected",
                "deleted_at",
                "created_at",
                "updated_at",
                "roles",
                "has_permission",
            ],
        ]);
    }

    public function test_show_returns_404_if_page_not_found(): void
    {
        // Arrange

        Sanctum::actingAs($this->site, ['*']);


        // Act
        $response = $this->getJson(route('site.page.view', ['permalink' => 'non-existent-page']));

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The selected permalink is invalid.',
            'errors' => [
                'permalink' => ['The selected permalink is invalid.'],
            ],
        ]);
    }

    public function test_show_validates_permalink_parameter(): void
    {

        Sanctum::actingAs($this->site, ['*']);
        $response = $this->getJson(route('site.page.view', ['permalink' => 's']));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['permalink']);
    }
}
