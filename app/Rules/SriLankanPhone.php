<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SriLankanPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = $this->normalize($value);

        if ($normalized === null) {
            $fail('The :attribute must be a valid Sri Lankan phone number (e.g. 0771234567 or +94771234567).');
        }
    }

    public static function normalize(string $value): ?string
    {
        $digits = preg_replace('/[\s\-]/', '', $value);

        if (preg_match('/^\+94(\d{9})$/', $digits, $m)) {
            return '+94' . $m[1];
        }

        if (preg_match('/^0(\d{9})$/', $digits, $m)) {
            return '+94' . $m[1];
        }

        return null;
    }
}
