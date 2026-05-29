<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;
use InvalidArgumentException;

class UploadService
{
    /** @var list<string> */
    protected array $forbiddenExtensions = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'js', 'html', 'htm', 'svg',
    ];

    public function storeImage(UploadedFile $file, string $folder, int $maxWidth = 1920): string
    {
        $this->assertAllowedFile($file, ['jpg', 'jpeg', 'png', 'webp', 'gif']);

        $filename = Str::uuid() . '.webp';
        $path = $folder . '/' . $filename;

        $encoded = Image::decodePath($file->getRealPath())
            ->scaleDown(width: $maxWidth)
            ->encode(new WebpEncoder(quality: 82));

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    public function storePdf(UploadedFile $file, string $folder): string
    {
        $this->assertAllowedFile($file, ['pdf']);

        $filename = Str::uuid() . '.pdf';
        $path = $folder . '/' . $filename;

        Storage::disk('public')->putFileAs($folder, $file, $filename);

        return $path;
    }

    public function storeAudio(UploadedFile $file, string $folder): string
    {
        $this->assertAllowedFile($file, ['mp3', 'm4a', 'ogg', 'wav']);

        $filename = Str::uuid() . '.' . $file->guessExtension();
        Storage::disk('public')->putFileAs($folder, $file, $filename);

        return $folder . '/' . $filename;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function storeFile(UploadedFile $file, string $folder, string $type): string
    {
        $allowedByType = match ($type) {
            'video' => ['mp4', 'webm'],
            'pdf'   => ['pdf'],
            'audio' => ['mp3', 'm4a', 'ogg'],
            default => [],
        };

        if ($allowedByType === []) {
            throw new InvalidArgumentException('Type de fichier non supporté.');
        }

        $this->assertAllowedFile($file, $allowedByType);

        $ext = $file->guessExtension() ?: $allowedByType[0];
        $filename = Str::uuid() . '.' . $ext;
        Storage::disk('public')->putFileAs($folder, $file, $filename);

        return $folder . '/' . $filename;
    }

    /**
     * @param  list<string>  $allowedExtensions
     */
    protected function assertAllowedFile(UploadedFile $file, array $allowedExtensions): void
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: '');

        if ($ext === '' || in_array($ext, $this->forbiddenExtensions, true)) {
            throw new InvalidArgumentException('Type de fichier interdit.');
        }

        if (! in_array($ext, $allowedExtensions, true)) {
            throw new InvalidArgumentException('Extension non autorisée pour cet upload.');
        }

        $this->assertMimeMatchesExtension($file, $ext);
    }

    protected function assertMimeMatchesExtension(UploadedFile $file, string $ext): void
    {
        $path = $file->getRealPath();
        if (! $path) {
            throw new InvalidArgumentException('Fichier invalide.');
        }

        $mime = mime_content_type($path) ?: '';
        $map = [
            'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'],
            'webp' => ['image/webp'], 'gif' => ['image/gif'], 'pdf' => ['application/pdf'],
            'mp3' => ['audio/mpeg', 'audio/mp3'], 'm4a' => ['audio/mp4', 'audio/x-m4a'],
            'ogg' => ['audio/ogg'], 'wav' => ['audio/wav', 'audio/x-wav'],
            'mp4' => ['video/mp4'], 'webm' => ['video/webm'],
        ];

        $allowedMimes = $map[$ext] ?? [];
        if ($allowedMimes !== [] && ! in_array($mime, $allowedMimes, true)) {
            throw new InvalidArgumentException('Le type MIME du fichier ne correspond pas à l\'extension.');
        }
    }
}
