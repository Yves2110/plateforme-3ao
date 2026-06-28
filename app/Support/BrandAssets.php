<?php

namespace App\Support;

final class BrandAssets
{
    public static function logoUrl(): string
    {
        return asset(config('brand.logo', 'images/logo-3ao.jpeg'));
    }

    public static function logoAlt(): string
    {
        return config('brand.logo_alt', '3AO');
    }
}
