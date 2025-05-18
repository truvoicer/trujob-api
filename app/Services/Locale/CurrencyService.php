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

    public function createCurrencyBatch(array $data) {
        $createBatch = Currency::create($data['currencies']);
        if (!$createBatch) {
            $this->addError('Error creating currency batch', $data);
            return false;
        }
        return true;
    }

    public function createCurrency(Country $country, array $data) {
        $currency = new Currency($data);
        $createCurrency = $country->currency()->save($currency);
        if (!$createCurrency) {
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


}
