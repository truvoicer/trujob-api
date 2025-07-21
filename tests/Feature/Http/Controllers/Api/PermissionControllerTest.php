<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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