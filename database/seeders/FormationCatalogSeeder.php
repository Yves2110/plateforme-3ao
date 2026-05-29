<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FormationCatalogSeeder extends Seeder
{
    /** @var array<string, array{top: string, bottom: string, accent: string}> */
    private array $palettes = [
        'atelier' => ['top' => '#2D6A4F', 'bottom' => '#40916C', 'accent' => '#F4C842'],
        'webinaire' => ['top' => '#1A1A2E', 'bottom' => '#2D6A4F', 'accent' => '#52B788'],
        'certification' => ['top' => '#1B4332', 'bottom' => '#2D6A4F', 'accent' => '#E85D04'],
        'cours' => ['top' => '#40916C', 'bottom' => '#74C69D', 'accent' => '#F4C842'],
        'default' => ['top' => '#2D6A4F', 'bottom' => '#52B788', 'accent' => '#D4A017'],
    ];

    public function run(): void
    {
        $this->call(FormationLmsDemoSeeder::class);

        $formations = Formation::query()->get();

        foreach ($formations as $formation) {
            if ($formation->thumbnail && Storage::disk('public')->exists($formation->thumbnail)) {
                continue;
            }

            $path = $this->generateCover($formation);
            $formation->update(['thumbnail' => $path]);
        }

        $this->command?->info('Vignettes de couverture générées pour '.$formations->count().' formation(s).');
    }

    private function generateCover(Formation $formation): string
    {
        $palette = $this->palettes[$formation->type] ?? $this->palettes['default'];
        $width = 800;
        $height = 500;

        $image = imagecreatetruecolor($width, $height);
        $top = $this->hexToRgb($palette['top']);
        $bottom = $this->hexToRgb($palette['bottom']);
        $accent = $this->hexToRgb($palette['accent']);

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / max($height - 1, 1);
            $r = (int) ($top[0] + ($bottom[0] - $top[0]) * $ratio);
            $g = (int) ($top[1] + ($bottom[1] - $top[1]) * $ratio);
            $b = (int) ($top[2] + ($bottom[2] - $top[2]) * $ratio);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }

        $accentColor = imagecolorallocatealpha($image, $accent[0], $accent[1], $accent[2], 40);
        imagefilledellipse($image, (int) ($width * 0.85), (int) ($height * 0.2), 220, 220, $accentColor);
        imagefilledellipse($image, (int) ($width * 0.15), (int) ($height * 0.75), 180, 180, $accentColor);

        $white = imagecolorallocate($image, 255, 255, 255);
        $gold = imagecolorallocate($image, $accent[0], $accent[1], $accent[2]);
        imagestring($image, 5, 32, 28, '3AO — FORMATION', $gold);
        imagestring($image, 5, 32, 52, strtoupper($formation->type), $white);

        $title = $this->wrapTitle($formation->title, 38);
        $y = 120;
        foreach ($title as $line) {
            imagestring($image, 5, 32, $y, $line, $white);
            $y += 22;
        }

        if ($formation->organizer) {
            imagestring($image, 3, 32, $height - 48, $this->ascii($formation->organizer), $gold);
        }

        $relative = 'formations/covers/'.$formation->slug.'.jpg';
        Storage::disk('public')->makeDirectory('formations/covers');

        $fullPath = Storage::disk('public')->path($relative);
        imagejpeg($image, $fullPath, 88);
        imagedestroy($image);

        return $relative;
    }

    /** @return array{0: int, 1: int, 2: int} */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /** @return list<string> */
    private function wrapTitle(string $title, int $maxLen): array
    {
        $title = $this->ascii($title);
        $words = explode(' ', $title);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if (strlen($candidate) <= $maxLen) {
                $current = $candidate;
            } else {
                if ($current !== '') {
                    $lines[] = $current;
                }
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return array_slice($lines, 0, 4);
    }

    private function ascii(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;

        return preg_replace('/[^\x20-\x7E]/', '', $text) ?? '';
    }
}
