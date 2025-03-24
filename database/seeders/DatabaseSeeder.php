<?php

namespace Database\Seeders;

use App\Enums\Auth\ApiAbility;
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
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\Role;
use App\Models\Site;
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
use Database\Seeders\admin\PageSeeder;
use Database\Seeders\admin\PermissionSeeder;
use Database\Seeders\admin\SettingSeeder;
use Database\Seeders\admin\SiteSeeder;
use Database\Seeders\firebase\FirebaseTopicSeeder;
use Database\Seeders\listing\BrandSeeder;
use Database\Seeders\listing\CategorySeeder;
use Database\Seeders\listing\ColorSeeder;
use Database\Seeders\listing\ListingTypeSeeder;
use Database\Seeders\listing\ProductTypeSeeder;
use Database\Seeders\locale\LocaleSeeder;
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
            SiteSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            LocaleSeeder::class,
            FirebaseTopicSeeder::class,
            ColorSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductTypeSeeder::class,
            ListingTypeSeeder::class,
            BlockSeeder::class,
            PageSeeder::class,
            PermissionSeeder::class,
            SettingSeeder::class
        ]);



        $listingFactory = Listing::factory()
            ->count(5)
            ->has(ListingFeature::factory()->count(1))
            ->has(ListingFeature::factory()->count(1))
            ->has(ListingReview::factory()->count(5))
            ->has(ListingFollow::factory()->count(5))
            ->has(ListingBrand::factory()->count(1))
            ->has(ListingColor::factory()->count(1))
            ->has(ListingMedia::factory()->count(5))
            ->has(ListingCategory::factory()->count(5))
            ->has(ListingProductType::factory()->count(5));


        $getSuperUserData = AuthService::getApiAbilityData(ApiAbility::SUPERUSER->value);
        if (!$getSuperUserData) {
            throw new \Exception('Error finding superuser ability data during seeding');
        }
        $findSuperUserRole = Role::where('name', $getSuperUserData['name'])->first();
        if (!$findSuperUserRole instanceof Role) {
            throw new \Exception('Error finding superuser role during seeding');
        }

        User::factory()
            ->has($listingFactory)
            ->has(UserFollow::factory()->count(5))
            ->has(UserProfile::factory()->count(1))
            ->has(UserReview::factory()->count(5))
            ->has(UserReward::factory()->count(5))
            ->has(UserSetting::factory()->count(1))
            ->has(UserMedia::factory()->count(1))
            ->has(
                MessagingGroup::factory()
                    ->has(MessagingGroupMessage::factory()->count(5))
                    ->count(5)
            )->create();

        User::factory()
            ->count(10)
            ->has($listingFactory)
            ->has(UserFollow::factory()->count(5))
            ->has(UserProfile::factory()->count(1))
            ->has(UserReview::factory()->count(5))
            ->has(UserReward::factory()->count(5))
            ->has(UserSetting::factory()->count(1))
            ->has(UserMedia::factory()->count(1))
            ->has(
                MessagingGroup::factory()
                    ->has(MessagingGroupMessage::factory()->count(5))
                    ->count(5)
            )
            ->create();

        $testUserData = DefaultData::TEST_USER_DATA;
        $user = $userAdminService->getUserRepository()->findOneBy(
            [['email', '=', $testUserData['email']]]
        );
        if (!$user instanceof User) {
            throw new \Exception("Error finding user");
        }
        $token = $userAdminService->createUserToken($user);
        $tokenData = [
            'user_token' => $token->plainTextToken,
        ];
        foreach (Site::all() as $site) {
            $siteToken = $siteService->createToken($site);
            if (!$siteToken) {
                throw new \Exception('Error creating site token');
            }
            $tokenData['sites'] = [];
            $tokenData['sites'][$site->slug] = $siteToken->plainTextToken;
        }
        $this->command->info('Tokens generated:');
        var_dump($tokenData);
    }
}
