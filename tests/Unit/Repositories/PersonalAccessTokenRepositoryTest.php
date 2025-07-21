<?php

namespace Tests\Unit\Repositories;

use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Repositories\PersonalAccessTokenRepository;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class PersonalAccessTokenRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PersonalAccessTokenRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PersonalAccessTokenRepository();
    }

    public function testGetModel(): void
    {
        $model = $this->repository->getModel();
        $this->assertInstanceOf(PersonalAccessToken::class, $model);
    }

    public function testUpdateTokenExpirySuccessfully(): void
    {
        $token = PersonalAccessToken::factory()->create();
        $expiresAt = now()->addDays(7)->format('Y-m-d H:i:s');
        $data = ['expires_at' => $expiresAt];

        $result = $this->repository->updateTokenExpiry($token, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->id,
            'expires_at' => $expiresAt,
        ]);
    }

    public function testUpdateTokenExpiryWithInvalidData(): void
    {
        $token = PersonalAccessToken::factory()->create();
        $data = ['invalid_data' => 'some value'];

        // Mock the update method to return false to simulate an error
        $mock = $this->mock(PersonalAccessTokenRepository::class);
        $mock->shouldReceive('update')
            ->with($data)
            ->once()
            ->andReturn(false);

        $this->app->instance(PersonalAccessTokenRepository::class, $mock);
        $repository = $this->app->make(PersonalAccessTokenRepository::class);

        $result = $repository->updateTokenExpiry($token, $data);
        $this->assertFalse($result);
    }

    public function testGetLatestAccessTokenReturnsToken(): void
    {
        $user = User::factory()->create();
        $token1 = $user->tokens()->create([
            'name' => 'token1',
            'token' => hash('sha256', 'token1'),
            'abilities' => ['*'],
            'expires_at' => now()->addDay(),
        ]);

        $token2 = $user->tokens()->create([
            'name' => 'token2',
            'token' => hash('sha256', 'token2'),
            'abilities' => ['*'],
            'expires_at' => now()->addDays(2),
        ]);

        $latestToken = $this->repository->getLatestAccessToken($user);

        $this->assertInstanceOf(PersonalAccessToken::class, $latestToken);
        $this->assertEquals($token2->id, $latestToken->id);
    }

    public function testGetLatestAccessTokenReturnsNullWhenNoValidTokenExists(): void
    {
        $user = User::factory()->create();

        $token1 = $user->tokens()->create([
            'name' => 'token1',
            'token' => hash('sha256', 'token1'),
            'abilities' => ['*'],
            'expires_at' => now()->subDay(),
        ]);

        $latestToken = $this->repository->getLatestAccessToken($user);

        $this->assertNull($latestToken);
    }

     public function testGetLatestAccessTokenReturnsNullWhenNoTokenExists(): void
    {
        $user = User::factory()->create();
        $latestToken = $this->repository->getLatestAccessToken($user);

        $this->assertNull($latestToken);
    }

    public function testGetLatestAccessTokenForSite(): void
    {
        $site = Site::factory()->create();
        $token = $site->tokens()->create([
            'name' => 'site_token',
            'token' => hash('sha256', 'site_token'),
            'abilities' => ['*'],
            'expires_at' => now()->addDay(),
        ]);

        $latestToken = $this->repository->getLatestAccessToken($site);

        $this->assertInstanceOf(PersonalAccessToken::class, $latestToken);
        $this->assertEquals($token->id, $latestToken->id);
    }

    public function testGetLatestAccessTokenForSiteUser(): void
    {
        $siteUser = SiteUser::factory()->create();
        $token = $siteUser->tokens()->create([
            'name' => 'site_user_token',
            'token' => hash('sha256', 'site_user_token'),
            'abilities' => ['*'],
            'expires_at' => now()->addDay(),
        ]);

        $latestToken = $this->repository->getLatestAccessToken($siteUser);

        $this->assertInstanceOf(PersonalAccessToken::class, $latestToken);
        $this->assertEquals($token->id, $latestToken->id);
    }
}