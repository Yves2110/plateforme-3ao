<?php

namespace App\Support;

use Illuminate\Validation\Rule;

final class ActualiteCategories
{
    public static function all(): array
    {
        return config('actualites.categories', []);
    }

    /** @return list<string> */
    public static function labels(): array
    {
        return array_keys(self::all());
    }

    public static function normalizeLabel(?string $category): string
    {
        if (! $category) {
            return 'Actualité';
        }

        if (isset(self::all()[$category])) {
            return $category;
        }

        return config('actualites.legacy_map.'.$category, 'Actualité');
    }

    public static function badgeClass(?string $category): string
    {
        $normalized = self::normalizeLabel($category);
        $badge = self::all()[$normalized]['badge'] ?? 'actualite';

        return 'badge-'.$badge;
    }

    /** @param  list<string>  $selected */
    public static function toggleFilter(array $selected, string $category): array
    {
        $category = self::normalizeLabel($category);
        $selected = array_values(array_filter($selected, fn ($c) => in_array($c, self::labels(), true)));

        if (in_array($category, $selected, true)) {
            return array_values(array_filter($selected, fn ($c) => $c !== $category));
        }

        $selected[] = $category;

        return $selected;
    }

    /** @param  list<string>  $selected */
    public static function filterUrl(array $selected, ?string $toggle = null, array $extra = []): string
    {
        if ($toggle !== null) {
            $selected = self::toggleFilter($selected, $toggle);
        }

        $params = array_filter($extra, fn ($v) => $v !== null && $v !== '');

        if ($selected !== []) {
            $params['categories'] = array_values($selected);
        }

        return route('actualites.index', $params);
    }

    /** @param  list<string>  $selected */
    public static function adminFilterUrl(array $selected, ?string $toggle = null, array $extra = []): string
    {
        if ($toggle !== null) {
            $selected = self::toggleFilter($selected, $toggle);
        }

        $params = array_filter($extra, fn ($v) => $v !== null && $v !== '');

        if ($selected !== []) {
            $params['categories'] = array_values($selected);
        }

        return route('admin.actualites.index', $params);
    }

    public static function categoryRule(): \Illuminate\Validation\Rules\In
    {
        return Rule::in(self::labels());
    }

    /** @return list<string> */
    public static function parseFilterInput(mixed $input): array
    {
        if (! is_array($input)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($c) => self::normalizeLabel(is_string($c) ? $c : null),
            $input
        ), fn ($c) => in_array($c, self::labels(), true))));
    }

    /**
     * Valeurs possibles en base pour un filtre multi-catégories (inclut les anciennes valeurs).
     *
     * @param  list<string>  $canonicalSelected
     * @return list<string>
     */
    public static function storageValuesForFilter(array $canonicalSelected): array
    {
        $values = [];

        foreach ($canonicalSelected as $canonical) {
            $values[] = $canonical;

            foreach (config('actualites.legacy_map', []) as $legacy => $mapsTo) {
                if ($mapsTo === $canonical) {
                    $values[] = $legacy;
                }
            }
        }

        return array_values(array_unique($values));
    }
}
