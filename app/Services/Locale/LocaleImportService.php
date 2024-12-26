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
        try {
            $iso3Codes = file_get_contents(storage_path('app/locale/iso3.json'));
            $decodeIso3 = json_decode($iso3Codes, true);
            $names = file_get_contents(storage_path('app/locale/names.json'));
            $decodeNames = json_decode($names, true);
            $phone = file_get_contents(storage_path('app/locale/phone.json'));
            $decodePhone = json_decode($phone, true);
            $currencyCodes = file_get_contents(storage_path('app/locale/currency.json'));
            $decodeCurrencyCodes = json_decode($currencyCodes, true);
            $currencyInfoCodes = file_get_contents(storage_path('app/locale/currency-codes.json'));
            $decodeCurrencyInfoCodes = json_decode($currencyInfoCodes, true);
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
        } catch (\Exception $exception) {
            return $exception;
        }
        return true;
    }
}
