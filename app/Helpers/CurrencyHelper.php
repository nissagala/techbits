<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format(int $amount): string
    {
        return 'LKR ' . number_format($amount, 0);
    }
}
