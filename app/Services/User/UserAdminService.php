<?php

namespace App\Services\User;

use App\Enums\Auth\ApiAbility;
use App\Enums\Auth\ApiTokenExpiry;
use App\Enums\SiteStatus;
use App\Helpers\Db\DbHelpers;
use App\Models\Role;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserSetting;
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
        private AuthService $authService,
        private UserProfileService $userProfileService,
        private UserSettingService $userSettingService,
    ) {
        parent::__construct();
        $this->setUserRepository(new UserRepository());
        $this->personalAccessTokenRepository = new PersonalAccessTokenRepository();
        $this->roleRepository = new RoleRepository();
    }


    public function findByParams(string $sort, string $order, ?int $count = null)
    {
        $this->userRepository->setPagination(true);
        return $this->userRepository->findAllWithParams($sort, $order, $count);
    }
    public function findUserRoles(string $sort, string $order, ?int $count = null)
    {
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
    public function createSiteUser(Site $site, User $user, array $roles)
    {
        $siteUser = $site->users()->where('user_id', $user->id)->first();
        if ($siteUser instanceof SiteUser) {
            $update = $siteUser->update([
                'status' => 'active'
            ]);
            if (!$update) {
                return false;
            }
            $siteUser->roles()->sync($roles);
            return $siteUser;
        }
        $site->users()->attach($user, [
            'status' => 'active'
        ]);
        $siteUser->roles()->sync($roles);
    }

    public function getUserToken(User $model)
    {
        $token = $this->getlatestToken($model);
        if ($token) {
            return $token;
        }
        return $this->createUserToken($model);
    }

    public static function userTokenHasAbility(User $user, ApiAbility $ability): bool
    {
        return $user->tokenCan(AuthService::getApiAbility($ability));
    }

    public function getUserRoles(User $user)
    {
        $appUserRoleData = AuthService::getApiAbilityData(ApiAbility::APP_USER);
        return $this->roleRepository->fetchUserRoles($user, [$appUserRoleData['name']]);
    }

    public function createUserTokenByRole(User $user, Role $role, ?\DateTime $expiry = null)
    {
        return $user->createToken($role->name, [$role->ability], $expiry);
    }

    public function createSiteUserTokenByRole(SiteUser $siteUser, Role $role, ?\DateTime $expiry = null)
    {
        return $siteUser->createToken($role->name, [$role->ability], $expiry);
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

    public function registerSiteUser(Site $site, User $user): SiteUser
    {
        $siteUser = SiteUser::where('site_id', $site->id)
            ->where('user_id', $user->id)
            ->first();
        if (!$siteUser instanceof SiteUser) {
            $site->users()->attach($user->id, [
                'site_id' => $site->id,
                'status' => SiteStatus::ACTIVE,
            ]);
            $siteUser = SiteUser::where('site_id', $site->id)
                ->where('user_id', $user->id)
                ->first();
        }
        if (!$siteUser instanceof SiteUser) {
            throw new BadRequestHttpException("Error creating site user");
        }

        $siteUser->roles()->sync(
            $siteUser->user->roles()->get()
        );
        return $siteUser;
    }

    public function createSiteUserToken(SiteUser $siteUser, ?ApiTokenExpiry $expiry = ApiTokenExpiry::ONE_DAY)
    {
        return $this->createSiteUserTokenByRole(
            $siteUser,
            $this->getUserRole($siteUser->user),
            $this->buildExpiryDate($expiry)
        );
    }

    public function createUserToken(User $user, ?ApiTokenExpiry $expiry = ApiTokenExpiry::ONE_DAY)
    {

        return $this->createUserTokenByRole($user, $this->getUserRole($user), $this->buildExpiryDate($expiry));
    }

    public function getUserRole(User $user): ?Role
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
            return null;
        }
        return $role;
    }
    public function buildExpiryDate(?ApiTokenExpiry $expiry = ApiTokenExpiry::ONE_DAY)
    {
        if (empty($expiry)) {
            $expiry = ApiTokenExpiry::ONE_DAY;
        }
        if ($expiry !== ApiTokenExpiry::NEVER) {
            return new \DateTime($expiry->value);
        }
        return null;
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
        $this->userProfileService->setUser($user);
        $this->userProfileService->setSite($this->getSite());

        $this->userSettingService->setUser($user);
        $this->userSettingService->setSite($this->getSite());

        $roleIds = [];
        if (count($roles)) {
            $roleIds = $this->authService->getRoleIds($roles);
        }

        $userFields = (new User())->getFillable();
        $userProfileFields = (new UserProfile())->getFillable();
        $userSettingsFields = (new UserSetting())->getFillable();

        $userProfile = array_filter($data, function ($key) use ($userProfileFields) {
            return in_array($key, $userProfileFields);
        }, ARRAY_FILTER_USE_KEY);

        $userSettings = array_filter($data, function ($key) use ($userSettingsFields) {
            return in_array($key, $userSettingsFields);
        }, ARRAY_FILTER_USE_KEY);

        $userData = array_filter($data, function ($key) use ($userFields) {
            return in_array($key, $userFields);
        }, ARRAY_FILTER_USE_KEY);

        if (count($userProfile)) {
            if (!$this->userProfileService->updateUserProfile($userProfile)) {
                throw new \Exception('Error updating user profile');
            }
        }

        if (count($userSettings)) {
            if (!$this->userSettingService->updateUserSetting($userSettings)) {
                throw new \Exception('Error updating user settings');
            }
        }
        if (count($userData)) {
            if (count($roles) && !$this->userRepository->updateUser($user, $data, $roleIds)) {
                throw new \Exception('Error updating user');
            } else if (!count($roles) && !$this->userRepository->updateUser($user, $userData)) {
                throw new \Exception('Error updating user');
            }
        }
        
        return true;
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
