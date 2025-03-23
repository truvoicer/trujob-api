<?php
namespace App\Traits;

use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Repositories\PersonalAccessTokenRepository;
use App\Services\Admin\AuthService;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ApiTokenTrait
{

    public function __construct(
        private PersonalAccessTokenRepository $personalAccessTokenRepository
    ){}

    public function getlatestToken(User|Site $model) {
        $token = $this->personalAccessTokenRepository->getLatestAccessToken($model);
        if ($token instanceof PersonalAccessToken) {
            return $token;
        }
        return null;
    }

    public function findApiTokensByParams(User|Site $model, string $sort, string $order, ?int $count = null)
    {
        return $model->tokens()->orderBy($sort, $order)->limit($count)->paginate();
    }

    public function deleteExpiredTokens(User|Site $model)
    {
        return $model->tokens()->where('expires_at', '<', now())->delete();
    }

    public function deleteApiTokenById(int $id)
    {
        $apiToken = PersonalAccessToken::where('id', $id)->first();
        if (!$apiToken instanceof PersonalAccessToken) {
            throw new BadRequestHttpException("ApiToken does not exist in database...");
        }
        return $apiToken->delete();
    }

    public function deleteApiToken(PersonalAccessToken $apiToken)
    {
        return $apiToken->delete();
    }

    public function getPersonalAccessTokenRepository(): PersonalAccessTokenRepository
    {
        return $this->personalAccessTokenRepository;
    }

}