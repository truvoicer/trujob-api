<?php

namespace Database\Seeders;

use App\Enums\Auth\ApiAbility;
use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Block;
use App\Models\Listing;
use App\Models\ListingBrand;
use App\Models\ListingCategory;
use App\Models\ListingColor;
use App\Models\ListingFeature;
use App\Models\ListingFollow;
use App\Models\ListingMedia;
use App\Models\ListingProductType;
use App\Models\ListingReview;
use App\Models\Media;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\Role;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserFollow;
use App\Models\UserMedia;
use App\Models\UserProfile;
use App\Models\UserReview;
use App\Models\UserReward;
use App\Models\UserSetting;
use App\Services\Admin\AuthService;
use App\Services\Data\DefaultData;
use App\Services\Site\SiteService;
use App\Services\User\UserAdminService;
use Database\Seeders\admin\BlockSeeder;
use Database\Seeders\admin\MenuSeeder;
use Database\Seeders\admin\PageSeeder;
use Database\Seeders\admin\PermissionSeeder;
use Database\Seeders\admin\SettingSeeder;
use Database\Seeders\admin\SidebarSeeder;
use Database\Seeders\admin\SiteSeeder;
use Database\Seeders\admin\WidgetSeeder;
use Database\Seeders\firebase\FirebaseTopicSeeder;
use Database\Seeders\listing\BrandSeeder;
use Database\Seeders\listing\CategorySeeder;
use Database\Seeders\listing\ColorSeeder;
use Database\Seeders\listing\FeatureSeeder;
use Database\Seeders\listing\ListingSeeder;
use Database\Seeders\listing\ListingTypeSeeder;
use Database\Seeders\listing\ProductTypeSeeder;
use Database\Seeders\locale\LocaleSeeder;
use Database\Seeders\payment\PaymentGatewaySeeder;
use Database\Seeders\price\PriceTypeSeeder;
use Database\Seeders\user\RoleSeeder;
use Database\Seeders\user\UserSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(UserAdminService $userAdminService, SiteService $siteService): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            LocaleSeeder::class,
            FirebaseTopicSeeder::class,
            ColorSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            FeatureSeeder::class,
            ProductTypeSeeder::class,
            ListingTypeSeeder::class,
            PriceTypeSeeder::class,
            PaymentGatewaySeeder::class,
            BlockSeeder::class,
            PermissionSeeder::class,
            SettingSeeder::class,
            SiteSeeder::class,
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
