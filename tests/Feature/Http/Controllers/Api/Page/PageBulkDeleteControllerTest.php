<?php

namespace Tests\Feature\Api\Page;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageBulkDeleteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_bulk_delete_pages_success(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $pages = Page::factory(3)->create([
            'site_id' => $user->site_id,
        ]);

        $pageIds = $pages->pluck('id')->toArray();

        // Act
        $response = $this->postJson(route('api.pages.bulk-delete'), [
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
        Sanctum::actingAs($user, ['*']);

        // Simulate an error during deletion by passing non-existent IDs
        $pageIds = [99999, 99998];

        // Act
        $response = $this->postJson(route('api.pages.bulk-delete'), [
            'ids' => $pageIds,
        ]);

        // Assert
        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Pages could not be deleted.'
        ]);
    }

    public function test_bulk_delete_pages_validation_failure(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(route('api.pages.bulk-delete'), [
            'ids' => [], // Empty array should trigger validation error
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('ids');
    }

    public function test_bulk_delete_pages_unauthenticated(): void
    {
        // Act
        $response = $this->postJson(route('api.pages.bulk-delete'), [
            'ids' => [1, 2, 3],
        ]);

        // Assert
        $response->assertStatus(401); // or 403 depending on your app's auth setup
    }

    public function test_bulk_delete_pages_different_site(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $pages = Page::factory(3)->create(); // Creates pages for a different site

        $pageIds = $pages->pluck('id')->toArray();

        // Act
        $response = $this->postJson(route('api.pages.bulk-delete'), [
            'ids' => $pageIds,
        ]);

        // Assert
        $response->assertStatus(500); //Or appropriate error message
        $response->assertJson([
            'message' => 'Pages could not be deleted.'
        ]);
    }
}