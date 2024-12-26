<?php

namespace App\Services\Locale;

use App\Models\Country;
use App\Models\User;

class CountryService
{
    private Country $country;

    public static function fetchCountry(string|int $countryValue) {
        return Country::where('id', $countryValue)
            ->orWhere('iso2', $countryValue)
            ->orWhere('iso2', $countryValue)
            ->orWhere('iso3', $countryValue)
            ->first();
    }

    public function createCountryBatch(array $data) {
        $createCountryBatch = Country::create($data['countries']);
        if (!$createCountryBatch) {
            $this->addError('Error creating country batch', $data);
            return false;
        }
        return true;
    }
    public function createCountry(array $data) {
        $this->country = new Country($data);
        $createCountry = $this->country->save();
        if (!$createCountry) {
            $this->addError('Error creating country', $data);
            return false;
        }
        return true;
    }

    public function updateCountry(array $data) {
        $this->country->fill($data);
        $save = $this->country->save();
        if (!$save) {
            $this->addError('Error updating country', $data);
            return false;
        }
        return true;
    }

    public function deleteCountry() {
        if (!$this->country->delete()) {
            $this->addError('Error deleting country');
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
