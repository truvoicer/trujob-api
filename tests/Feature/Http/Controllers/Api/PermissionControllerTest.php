<?php

namespace Tests\Feature;

use App\Models\Permission;

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
use Tests\TestCase;

class PermissionControllerTest extends TestCase
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
    
    public function it_can_get_all_permissions(): void
    {
        Permission::factory()->count(3)->create();

        $response = $this->getJson(route('permissions.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'label',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    
    public function it_can_get_a_single_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->getJson(route('permissions.show', $permission));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'label',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => $permission->label,
            ]);
    }

    
    public function it_can_create_a_permission(): void
    {
        $data = [
            'name' => $this->faker->unique()->word,
            'label' => $this->faker->sentence,
        ];

        $response = $this->postJson(route('permissions.store'), $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'label',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'name' => $data['name'],
                'label' => $data['label'],
            ]);

        $this->assertDatabaseHas('permissions', $data);
    }

    
    public function it_can_update_a_permission(): void
    {
        $permission = Permission::factory()->create();

        $data = [
            'name' => $this->faker->unique()->word,
            'label' => $this->faker->sentence,
        ];

        $response = $this->putJson(route('permissions.update', $permission), $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'label',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'name' => $data['name'],
                'label' => $data['label'],
            ]);

        $this->assertDatabaseHas('permissions', $data);
    }

    
    public function it_can_delete_a_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->deleteJson(route('permissions.destroy', $permission));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);
    }
}