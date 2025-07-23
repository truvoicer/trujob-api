<?php

namespace App\Services\Locale;

use App\Exceptions\UpdateException;
use App\Models\Country;
use App\Services\BaseService;

class CountryService extends BaseService
{

    public function createCountryBatch(array $data)
    {
        foreach ($data as $countryData) {
            $this->createCountry($countryData);
        }
        return true;
    }
    public function createCountry(array $data)
    {
        $country = new Country($data);
        if (!$country->save()) {
            throw new \Exception('Error creating country');
        }
        return true;
    }

    public function updateCountry(Country $country, array $data)
    {
        if (empty($data)) {
            throw new UpdateException('No data provided for update');
        }
        if (!$country->update($data)) {
            throw new UpdateException('Error updating country');
        }
        return true;
    }

    public function deleteCountry(Country $country)
    {
        if (!$country->delete()) {
            throw new \Exception('Error deleting country');
        }
        return true;
    }

    public function deleteCountryBatch(array $data)
    {
        $countryIds = $data['ids'] ?? [];
        if (empty($countryIds)) {
            throw new \Exception('No countries provided for deletion');
        }

        $deletedCount = Country::whereIn('id', $countryIds)->delete();
        if ($deletedCount === 0) {
            throw new \Exception('Error deleting country batch');
        }
        return true;
    }
}
