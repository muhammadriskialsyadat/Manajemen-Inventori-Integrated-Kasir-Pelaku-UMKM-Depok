<?php

namespace App\Support;

class Rupiah
{
    /**
     * Format a numeric value as Indonesian Rupiah (thousands separated by ".").
     * Contoh: 1500000 -> "1.500.000".
     */
    public static function format(float|int|string|null $value): string
    {
        return number_format((float) ($value ?? 0), 0, ',', '.');
    }

    /**
     * Same as format() but prefixed with "Rp ".
     * Contoh: 1500000 -> "Rp 1.500.000".
     */
    public static function formatWithPrefix(float|int|string|null $value): string
    {
        return 'Rp '.self::format($value);
    }
}
