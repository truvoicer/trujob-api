<?php

namespace App\Services\User;

use App\Enums\Auth\ApiAbility;
use App\Enums\Auth\ApiTokenExpiry;
use App\Helpers\Db\DbHelpers;
use App\Models\Role;
use App\Models\User;
use App\Repositories\PersonalAccessTokenRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthService;
use App\Services\BaseService;
use App\Traits\ApiTokenTrait;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAdminService extends BaseService
{
    use ApiTokenTrait;

    private PersonalAccessTokenRepository $personalAccessTokenRepository;
    private RoleRepository $roleRepository;

    public function __construct(
        private AuthService $authService
    )
    {
        parent::__construct();
        $this->setUserRepository(new UserRepository());
        $this->personalAccessTokenRepository = new PersonalAccessTokenRepository();
        $this->roleRepository = new RoleRepository();
    }


    public function findByParams(string $sort, string $order, ?int $count = null) {
        $this->userRepository->setPagination(true);
        return $this->userRepository->findAllWithParams($sort, $order, $count);
    }
    public function findUserRoles(string $sort, string $order, ?int $count = null) {
        return $this->roleRepository->findAllWithParams($sort, $order, $count);
    }

    public function createUserByRoleId(array $userData, array $roles)
    {
        return $this->createUser($userData, $roles);
    }
    public function createUser(array $userData, array $roles)
    {
        $roleIds = $this->authService->getRoleIds($roles);
        return $this->userRepository->createUser($userData, $roleIds);
    }

    public function getUserToken(User $model) {
        $token = $this->getlatestToken($model);
        if ($token) {
            return $token;
        }
        return $this->createUserToken($model);
    }

    public static function userTokenHasAbility(User $user, string $ability) {
        return $user->tokenCan(AuthService::getApiAbility($ability));
    }

    public function getUserRoles(User $user) {
        $appUserRoleData = AuthService::getApiAbilityData(ApiAbility::APP_USER->value);
        return $this->roleRepository->fetchUserRoles($user, [$appUserRoleData['name']]);
    }

    public function createUserTokenByRole(User $user, Role $role, ?\DateTime $expiry = null)
    {
        return $user->createToken($role->name, [$role->ability], $expiry);
    }
    
    /**
     * @throws \Exception
     */
    public function createUserTokenByRoleId(User $user, int $roleId, ?ApiTokenExpiry $expiry = ApiTokenExpiry::ONE_DAY)
    {
        $role = $this->roleRepository::findUserRoleBy($user, ['role_id' => $roleId]);
        if (!$role instanceof Role) {
            return false;
        }

        if (empty($expiry)) {
            $expiry = ApiTokenExpiry::ONE_DAY;
        }
        
        if ($expiry !== ApiTokenExpiry::NEVER) {
            $expiryDate = new \DateTime($expiry->value);
        } else {
            $expiryDate = null;
        }

        return $this->createUserTokenByRole($user, $role, $expiryDate);
    }
    public function createUserToken(User $user, ?ApiTokenExpiry $expiry = ApiTokenExpiry::ONE_DAY)
    {
        $availableRoles = AuthService::DEFAULT_ROLES;
        $roles = $user->roles()
            ->whereIn('name', array_column($availableRoles, 'name'))
            ->get();
        $roles = $roles->sort(function ($a, $b) use ($availableRoles) {
            $aRole = array_search($a->name, array_column($availableRoles, 'name'));
            $bRole = array_search($b->name, array_column($availableRoles, 'name'));
            if ($aRole === $bRole) {
                return 0;
            }
            return ($aRole < $bRole) ? -1 : 1;
        });

        $role = $roles->first();
        if (!$role instanceof Role) {
            return false;
        }
        
        if (empty($expiry)) {
            $expiry = ApiTokenExpiry::ONE_DAY;
        }
        
        if ($expiry !== ApiTokenExpiry::NEVER) {
            $expiryDate = new \DateTime($expiry->value);
        } else {
            $expiryDate = null;
        }
        return $this->createUserTokenByRole($user, $role, $expiryDate);
    }

    public function getUserByEmail(string $email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function updateApiTokenExpiry(PersonalAccessToken $apiToken, array $data)
    {
        return $this->personalAccessTokenRepository->updateTokenExpiry(
            $apiToken,
            $data
        );
    }

    public function updateUser(User $user, array $data, ?array $roles = [])
    {
        $roleIds = $this->authService->getRoleIds($roles);
        return $this->userRepository->updateUser($user, $data, $roleIds);
    }

    public function deleteUser(User $user)
    {
        $this->userRepository->setModel($user);
        return $this->userRepository->delete();
    }

    public function deleteBatchUser(array $ids)
    {
        if (!count($ids)) {
            throw new BadRequestHttpException("No user ids provided.");
        }
        return $this->userRepository->deleteBatch($ids);
    }

}
