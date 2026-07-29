<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Format;

class ImageOptimizerService
{
    private const MAX_WIDTH = 2200;
    private const MAX_HEIGHT = 2200;
    private const QUALITY = 90;

    public function optimize(UploadedFile $file, string $directory = 'wisma-photos'): string
    {
        $manager = ImageManager::usingDriver(Driver::class);

        $image = $manager->decodeSplFileInfo($file);

        $image->scaleDown(width: self::MAX_WIDTH, height: self::MAX_HEIGHT);

        $filename = $directory . '/' . Str::random(40) . '.webp';

        $encoded = $image->encodeUsingFormat(Format::WEBP, quality: self::QUALITY);

        Storage::disk('private')->put($filename, (string) $encoded);

        return $filename;
    }
}