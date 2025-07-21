<?php

namespace Tests\Feature\Api\Page;

use App\Models\Page;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Page\PageService;
use Mockery;
use Mockery\MockInterface;

class SitePageControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_index_returns_empty_array(): void
    {
        $response = $this->getJson(route('api.pages.index'));

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_show_returns_page_resource_on_success(): void
    {
        // Arrange
        $site = Site::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);

        $page = Page::factory()->create([
            'site_id' => $site->id,
            'permalink' => 'test-page',
        ]);

        $page->load(['roles', 'pageBlocks', 'sidebars']);

        // Mock SiteHelper::getCurrentSite() to return the created site and user
        $this->partialMock('App\Helpers\SiteHelper', function (MockInterface $mock) use ($site, $user) {
            $mock->shouldReceive('getCurrentSite')->once()->andReturn([$site, $user]);
        });

        // Mock PageService
        $this->mock(PageService::class, function (MockInterface $mock) use ($site, $page) {
            $mock->shouldReceive('getPageByPermalink')
                ->with($site, 'test-page')
                ->once()
                ->andReturn($page);
        });


        // Act
        $response = $this->getJson(route('api.pages.show', ['permalink' => 'test-page']));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'site_id',
                'title',
                'permalink',
                'content',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_show_returns_404_if_page_not_found(): void
    {
        // Arrange
        $site = Site::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);

        // Mock SiteHelper::getCurrentSite() to return the created site and user
        $this->partialMock('App\Helpers\SiteHelper', function (MockInterface $mock) use ($site, $user) {
            $mock->shouldReceive('getCurrentSite')->once()->andReturn([$site, $user]);
        });

        // Mock PageService to return null (page not found)
        $this->mock(PageService::class, function (MockInterface $mock) use ($site) {
            $mock->shouldReceive('getPageByPermalink')
                ->with($site, 'non-existent-page')
                ->once()
                ->andReturn(null);
        });


        // Act
        $response = $this->getJson(route('api.pages.show', ['permalink' => 'non-existent-page']));

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Page not found']);
    }

    public function test_show_validates_permalink_parameter(): void
    {
        $response = $this->getJson(route('api.pages.show', ['permalink' => '']));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['permalink']);
    }
}