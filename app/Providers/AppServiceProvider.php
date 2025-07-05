<?php

namespace App\Providers;

use App\Enums\MorphEntity;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap(
            array_combine(
                array_map(
                    fn(MorphEntity $entity) => $entity->value,
                    MorphEntity::cases()
                ),
                array_map(
                    fn(MorphEntity $entity) => $entity->getClass(),
                    MorphEntity::cases()
                )
            )
        );
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $site = $user->sites()->where('password_reset', true)->first();
            if (!$site) {
                throw new \Exception("User does not have an associated site.");
            }

            if (!$site->settings?->frontend_url) {
                throw new \Exception("Site does not have a frontend URL.");
            }
            $queryArray = [
                'email' => $user->email,
                'token' => $token,
            ];
            $query = http_build_query($queryArray);
            $encodedQuery = base64_encode($query);
            return $site->settings->frontend_url.'/password/reset/confirmation?token='.$encodedQuery;
        });
    }
}
