<?php

namespace Tests\Feature\Api\Page;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use App\Enums\ViewType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewControllerTest extends TestCase
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

    public function it_can_return_index_data()
    {
        $response = $this->getJson(route('page-view.index'));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page view controller index method',
                'data' => collect(ViewType::cases())->pluck('value')->toArray(),
            ]);
    }
}