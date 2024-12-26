<?php

namespace App\Services\Locale;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Storage;

class LocaleImportService
{

    public function runImport()
    {
        $iso3Codes = file_get_contents(database_path('data/locale/iso3.json'));
        if (!$iso3Codes) {
            throw new \Exception('Error reading iso3.json file ' . database_path('data/locale/iso3.json'));
        }
        $decodeIso3 = json_decode($iso3Codes, true);
        if (!$decodeIso3) {
            throw new \Exception('Error decoding iso3.json file');
        }
        $names = file_get_contents(database_path('data/locale/names.json'));
        if (!$names) {
            throw new \Exception('Error reading names.json file');
        }
        $decodeNames = json_decode($names, true);
        if (!$decodeNames) {
            throw new \Exception('Error decoding names.json file');
        }
        $phone = file_get_contents(database_path('data/locale/phone.json'));
        if (!$phone) {
            throw new \Exception('Error reading phone.json file');
        }
        $decodePhone = json_decode($phone, true);
        if (!$decodePhone) {
            throw new \Exception('Error decoding phone.json file');
        }
        $currencyCodes = file_get_contents(database_path('data/locale/currency.json'));
        if (!$currencyCodes) {
            throw new \Exception('Error reading currency.json file');
        }
        $decodeCurrencyCodes = json_decode($currencyCodes, true);
        if (!$decodeCurrencyCodes) {
            throw new \Exception('Error decoding currency.json file');
        }
        $currencyInfoCodes = file_get_contents(database_path('data/locale/currency-codes.json'));
        if (!$currencyInfoCodes) {
            throw new \Exception('Error reading currency-codes.json file');
        }
        $decodeCurrencyInfoCodes = json_decode($currencyInfoCodes, true);
        if (!$decodeCurrencyInfoCodes) {
            throw new \Exception('Error decoding currency-codes.json file');
        }
        foreach ($decodeNames as $iso2Code => $countryName) {
            $iso3Code = $decodeIso3[$iso2Code];
            $phoneCode = $decodePhone[$iso2Code];
            $currencyCode = $decodeCurrencyCodes[$iso2Code];
            $country = new Country([
                'name' => $countryName,
                'iso2' => $iso2Code,
                'iso3' => $iso3Code,
                'phone_code' => $phoneCode
            ]);
            $country->save();
            if (array_key_exists($currencyCode, $decodeCurrencyInfoCodes)) {
                $currencyInfoCode = $decodeCurrencyInfoCodes[$currencyCode];
                $currency = new Currency([
                    'name' => $currencyInfoCode['name'],
                    'name_plural' => $currencyInfoCode['name_plural'],
                    'code' => $currencyInfoCode['code'],
                    'symbol' => $currencyInfoCode['symbol']
                ]);
                $country->currency()->save($currency);
            }
        }
    }
}
