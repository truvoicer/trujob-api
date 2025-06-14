<?php

namespace App\Providers;

use App\Enums\MorphEntity;
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
    }
}
