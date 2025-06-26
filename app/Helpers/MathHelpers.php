<?php

namespace App\Helpers;

class MathHelpers
{

    public static function toDecimalPlaces(float $number, int $decimalPlaces = 2): float
    {
        // Ensure decimalPlaces is not negative
        if ($decimalPlaces < 0) {
            $decimalPlaces = 0;
        }

        // Calculate the multiplier (e.g., 100 for 2 decimal places, 1000 for 3)
        $multiplier = pow(10, $decimalPlaces);

        // Determine the sign of the number
        $sign = ($number < 0) ? -1 : 1;

        // To truncate (cut off) effectively with floor for both positive and negative numbers:
        // 1. Take the absolute value.
        // 2. Multiply by the multiplier to shift desired decimals to integer part.
        // 3. Apply floor() to remove fractional part.
        // 4. Divide by multiplier to shift decimals back.
        // 5. Reapply the original sign.
        $truncatedNumber = floor(abs($number) * $multiplier) / $multiplier;

        return $truncatedNumber * $sign;
    }
}
