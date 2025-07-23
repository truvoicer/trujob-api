<?php

namespace App\Services\Locale;

use App\Models\Country;
use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use App\Services\BaseService;

class CurrencyService extends BaseService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    )
    {

    }

    public static function fetchCurrency(string|int $currencyValue) {
        return Currency::where('id', $currencyValue)
            ->orWhere('iso2', $currencyValue)
            ->orWhere('iso2', $currencyValue)
            ->orWhere('iso3', $currencyValue)
            ->first();
    }

    public function createCurrencyBatch(array $currencies) {
        foreach ($currencies as $currencyData) {
            $this->createCurrency($currencyData);
        }
        return true;
    }

    public function createCurrency(array $data) {
        $currency = new Currency($data);
        if (!$currency->save()) {
            throw new \Exception('Error creating currency');
        }
        return true;
    }

    public function updateCurrency(Currency $currency, array $data) {
        if (!$currency->update($data)) {
            throw new \Exception('Error updating currency');
        }
        return true;
    }

    public function deleteCurrency(Currency $currency) {
        if (!$currency->delete()) {
            throw new \Exception('Error deleting currency');
        }
        return true;
    }

    public function deleteCurrencyBatch(array $ids) {
        if (empty($ids)) {
            throw new \Exception('No currencies provided for deletion');
        }

        $deletedCount = Currency::whereIn('id', $ids)->delete();
        if ($deletedCount === 0) {
            throw new \Exception('Error deleting currency batch');
        }
        return true;
    }

}
