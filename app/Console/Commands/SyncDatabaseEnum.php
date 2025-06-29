<?php

namespace App\Console\Commands;

use App\Enums\DatabaseEnum;
use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\Shipping\OrderShipmentStatus;
use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDatabaseEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-db-enum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default pages and menus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $availableEnums = DatabaseEnum::cases();

        $sortedEnums = collect($availableEnums)
            ->mapWithKeys(function ($enum) {
                return [$enum->value => $enum->label()];
            })
            ->sort()
            ->toArray();
        $enum = $this->ask(
            sprintf(
                'Enter the enum name to sync, you can choose from: (%s)',
                implode(', ', array_keys($sortedEnums))
            )
        );
        if (!array_key_exists($enum, $sortedEnums)) {
            $this->error(sprintf('Enum %s does not exist', $enum));
            return;
        }
        $enumClass = DatabaseEnum::from($enum);
        $this->info(sprintf('Syncing %s', $enumClass->label()));
        $modelColumns = $enumClass->model();
        if (empty($modelColumns)) {
            $this->error(sprintf('No model found for %s', $enumClass->label()));
            return;
        }
        $modelClass = array_key_first($modelColumns);
        if (!class_exists($modelClass)) {
            $this->error(sprintf('Model %s does not exist', $modelClass));
            return;
        }
        $modelInstance = new $modelClass();
        if (!method_exists($modelInstance, 'getFillable')) {
            $this->error(sprintf('Model %s does not have a getFillable method', $modelClass));
            return;
        }
        if (!method_exists($modelInstance, 'getTable')) {
            $this->error(sprintf('Model %s does not have a getTable method', $modelClass));
            return;
        }
        if (empty($modelColumns[$modelClass]) || !is_array($modelColumns[$modelClass])) {
            $this->error(sprintf('No columns found for model %s', $modelClass));
            return;
        }
        // update table column enums
        foreach ($modelColumns[$modelClass] as $column) {
            if (!in_array($column, $modelInstance->getFillable())) {
                $this->error(sprintf('Column %s does not exist in model %s', $column, $modelClass));
                continue;
            }
            $enumValues = $enumClass->enum()::cases();
            $enumValues = array_map(fn($e) => $e->value, $enumValues);
            $this->info(sprintf('Updating column %s in model %s with values: %s', $column, $modelClass, implode(', ', $enumValues)));
            $query = sprintf(
                    'ALTER TABLE %s MODIFY %s ENUM(%s)',
                    $modelInstance->getTable(),
                    $column,
                    implode(', ', array_map(fn($e) => "'$e'", $enumValues))
                );
            $confirm = $this->confirm(
                sprintf('Are you sure you want to run the following query? %s', $query),
                false
            );
            if (!$confirm) {
                continue;
            }
            DB::statement(
                $query
            );
        }
        $this->info(sprintf('Successfully synced %s', $enumClass->label()));
        $this->info('You may need to run `php artisan migrate` to apply the changes to the database.');
        $this->info('You may also need to run `php artisan cache:clear` to clear the cache.');
        $this->info('You may also need to run `php artisan config:clear` to clear the config cache.');
        $this->info('You may also need to run `php artisan route:clear` to clear the route cache.');
        $this->info('You may also need to run `php artisan view:clear` to clear the view cache.');
    }
}
