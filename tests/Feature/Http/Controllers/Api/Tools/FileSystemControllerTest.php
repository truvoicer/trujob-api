<?php

namespace Tests\Feature;


use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use App\Models\File;
use App\Services\Tools\FileSystem\FileSystemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileSystemControllerTest extends TestCase
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
        Sanctum::actingAs($this->siteUser, ['*']);
    }
    
    public function it_can_download_a_file()
    {
        $file = File::factory()->create();

        $response = $this->getJson(route('file-system.download-file', ['file' => $file->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'url'
                ]
            ]);
    }

    
    public function it_can_get_a_list_of_files()
    {
        File::factory()->count(3)->create();

        $response = $this->getJson(route('file-system.get-files'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'filename',
                        'path',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    
    public function it_can_get_a_single_file()
    {
        $file = File::factory()->create();

        $response = $this->getJson(route('file-system.get-single-file', ['file' => $file->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'filename',
                    'path',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    
    public function it_can_delete_a_file()
    {
        $file = File::factory()->create();

        $response = $this->deleteJson(route('file-system.delete-file', ['file' => $file->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File deleted.',
            ]);

        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }

    
    public function it_returns_an_error_response_when_file_deletion_fails()
    {
        // Mock the FileSystemService to simulate a failed deletion
        $this->mock(FileSystemService::class, function ($mock) {
            $mock->shouldReceive('deleteFile')
                ->once()
                ->andReturn(false); // Simulate a failed deletion
        });

        $file = File::factory()->create();

        $response = $this->deleteJson(route('file-system.delete-file', ['file' => $file->id]));

        $response->assertStatus(500) // or appropriate error status code
            ->assertJson([
                'success' => false,
                'message' => 'Error deleting file',
            ]);

        $this->assertDatabaseHas('files', ['id' => $file->id]); // Assert file still exists
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('filesystems.default', 'local');
    }

    protected function defineRoutes($router)
    {
        $router->get('file-system/download/{file}', [\App\Http\Controllers\Api\Tools\FileSystemController::class, 'downloadFile'])->name('file-system.download-file');
        $router->get('file-system', [\App\Http\Controllers\Api\Tools\FileSystemController::class, 'getFiles'])->name('file-system.get-files');
        $router->get('file-system/{file}', [\App\Http\Controllers\Api\Tools\FileSystemController::class, 'getSingleFile'])->name('file-system.get-single-file');
        $router->delete('file-system/{file}', [\App\Http\Controllers\Api\Tools\FileSystemController::class, 'deleteFile'])->name('file-system.delete-file');
    }
}