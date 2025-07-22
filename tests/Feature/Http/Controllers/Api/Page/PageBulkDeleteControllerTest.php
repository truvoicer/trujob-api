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
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PageBulkDeleteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


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

    public function test_bulk_delete_pages_success(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $pages = Page::factory(3)->create([
            'site_id' => $this->site->id,
        ]);

        $pageIds = $pages->pluck('id')->toArray();

        Sanctum::actingAs($this->siteUser, ['*']);
        // Act
        $response = $this->deleteJson(route('page.bulk.destroy'), [
            'ids' => $pageIds,
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Pages deleted successfully.'
        ]);

        $this->assertDatabaseMissing('pages', ['id' => $pageIds[0]]);
        $this->assertDatabaseMissing('pages', ['id' => $pageIds[1]]);
        $this->assertDatabaseMissing('pages', ['id' => $pageIds[2]]);
    }

    public function test_bulk_delete_pages_failure(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        // Simulate an error during deletion by passing non-existent IDs
        $pageIds = [99999, 99998];

        // Act
        $response = $this->deleteJson(route('page.bulk.destroy'), [
            'ids' => $pageIds,
        ]);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('ids.0');
        $response->assertJsonValidationErrors('ids.1');
    }

    public function test_bulk_delete_pages_validation_failure(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->deleteJson(route('page.bulk.destroy'), [
            'ids' => [], // Empty array should trigger validation error
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('ids');
    }

    public function test_bulk_delete_pages_unauthenticated(): void
    {
        // Act
        $response = $this->deleteJson(route('page.bulk.destroy'), [
            'ids' => [1, 2, 3],
        ]);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED); // or 403 depending on your app's auth setup
    }

    public function test_bulk_delete_pages_different_site(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        $pages = Page::factory(3)->create([
            'site_id' => $site->id,
        ]); // Creates pages for a different site

        $pageIds = $pages->pluck('id')->toArray();

        // Act
        $response = $this->deleteJson(route('page.bulk.destroy'), [
            'ids' => $pageIds,
        ]);

        // Assert

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('ids.0');
        $response->assertJsonValidationErrors('ids.1');
        $response->assertJsonValidationErrors('ids.2');
    }
}
