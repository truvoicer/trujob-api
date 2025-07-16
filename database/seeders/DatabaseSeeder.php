<?php

namespace Database\Seeders;

use App\Enums\Auth\ApiAbility;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Services\Admin\AuthService;
use App\Services\Data\DefaultData;
use App\Services\Site\SiteService;
use App\Services\User\RoleService;
use App\Services\User\UserAdminService;
use Database\Seeders\admin\BlockSeeder;
use Database\Seeders\admin\MenuSeeder;
use Database\Seeders\admin\PageSeeder;
use Database\Seeders\admin\PermissionSeeder;
use Database\Seeders\admin\SettingSeeder;
use Database\Seeders\admin\SidebarSeeder;
use Database\Seeders\admin\SiteSeeder;
use Database\Seeders\admin\WidgetSeeder;
use Database\Seeders\discount\DiscountSeeder;
use Database\Seeders\firebase\FirebaseTopicSeeder;
use Database\Seeders\locale\CountrySeeder;
use Database\Seeders\product\BrandSeeder;
use Database\Seeders\product\CategorySeeder;
use Database\Seeders\product\ColorSeeder;
use Database\Seeders\product\FeatureSeeder;
use Database\Seeders\locale\CurrencySeeder;
use Database\Seeders\locale\RegionSeeder;
use Database\Seeders\payment\PaymentGatewaySeeder;
use Database\Seeders\payment\SitePaymentGatewaySeeder;
use Database\Seeders\product\ProductCategorySeeder;
use Database\Seeders\shipping\ShippingMethodSeeder;
use Database\Seeders\shipping\ShippingZoneSeeder;
use Database\Seeders\tax\TaxRateSeeder;
use Database\Seeders\user\RoleSeeder;
use Database\Seeders\user\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(UserAdminService $userAdminService, SiteService $siteService, RoleService $roleService): void
    {
        $this->call([
            RoleSeeder::class,
            // UserSeeder::class,
            // LocaleSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            RegionSeeder::class,
            LanguageSeeder::class,
            ShippingZoneSeeder::class,
            ShippingMethodSeeder::class,
            TaxRateSeeder::class,
            FirebaseTopicSeeder::class,
            ColorSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            FeatureSeeder::class,
            ProductCategorySeeder::class,
            PaymentGatewaySeeder::class,
            BlockSeeder::class,
            PermissionSeeder::class,
            SettingSeeder::class,
            SiteSeeder::class,
            SitePaymentGatewaySeeder::class,
            DiscountSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
            WidgetSeeder::class,
            SidebarSeeder::class,
        ]);


        $getSuperUserData = AuthService::getApiAbilityData(ApiAbility::SUPERUSER->value);
        if (!$getSuperUserData) {
            throw new \Exception('Error finding superuser ability data during seeding');
        }
        $findSuperUserRole = Role::where('name', $getSuperUserData['name'])->first();
        if (!$findSuperUserRole instanceof Role) {
            throw new \Exception('Error finding superuser role during seeding');
        }

        $testUserData = DefaultData::TEST_USER_DATA;
        $user = $userAdminService->getUserRepository()->findOneBy(
            [['email', '=', $testUserData['email']]]
        );
        if (!$user instanceof User) {
            throw new \Exception("Error finding user");
        }

        $user->roles()->syncWithoutDetaching([$findSuperUserRole->id]);

        $tokenData = [];
        foreach (Site::all() as $site) {
            $siteToken = $siteService->createToken($site);
            if (!$siteToken) {
                throw new \Exception('Error creating site token');
            }

            $siteUserToken = $userAdminService->createSiteUserToken(
                $userAdminService->registerSiteUser($site, $user)
            );
            $tokenData['sites'] = [];
            $tokenData['sites'][$site->name] = [];
            $tokenData['sites'][$site->name]['user_token'] = $siteUserToken->plainTextToken;
            $tokenData['sites'][$site->name]['site_token'] = $siteToken->plainTextToken;
        }
        $this->command->info('Tokens generated:');
        var_dump($tokenData);
    }
}
