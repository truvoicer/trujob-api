<?php

namespace Tests\Feature\Api\Pagination;

use App\Enums\Pagination\PaginationScrollType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;

class PaginationScrollTypeControllerTest extends TestCase
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

    public function test_it_returns_a_json_response_with_pagination_scroll_types(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $response = $this->getJson(route('pagination.scroll.type'));

        $response->assertOk()
            ->assertJsonStructure([
                'data'
            ]);

        $response->assertJson([
            'data' => array_map(
                fn(PaginationScrollType $item) => $item->value,
                PaginationScrollType::cases()
            )
        ]);
    }

}
