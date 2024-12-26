<?php

namespace App\Services\Locale;

use App\Models\Country;
use App\Models\Currency;
use App\Models\User;

class CurrencyService
{
    private Currency $currency;
    private Country $country;

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

    public function createCurrency(array $data) {
        $this->currency = new Currency($data);
        $createCurrency = $this->country->currency()->save($this->currency);
        if (!$createCurrency) {
            $this->addError('Error creating currency', $data);
            return false;
        }
        return true;
    }

    public function updateCurrency(array $data) {
        $this->currency->fill($data);
        $save = $this->currency->save();
        if (!$save) {
            $this->addError('Error updating currency', $data);
            return false;
        }
        return true;
    }

    public function deleteCurrency() {
        if (!$this->currency->delete()) {
            $this->addError('Error deleting currency');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $error
     */
    public function addError(string $message, ?array $data = []): void
    {
        $error = [
            'message' => $message
        ];
        if (count($data)) {
            $error['data'] = $data;
        }
        $this->errors[] = $error;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

}
