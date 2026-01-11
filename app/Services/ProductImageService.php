<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    private const DISK = 'public';
    private const FOLDER = 'products';

    /**
     * Upload an image to local storage
     *
     * @param UploadedFile $file
     * @return string Relative path to the image
     */
    public function upload(UploadedFile $file): string
    {
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store the file in the 'products' folder on the 'public' disk
        $path = $file->storeAs(self::FOLDER, $filename, self::DISK);

        return $path;
    }

    /**
     * Delete an image from local storage
     *
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk(self::DISK)->exists($path)) {
            return Storage::disk(self::DISK)->delete($path);
        }

        return false;
    }

    /**
     * Get the full URL for an image path
     *
     * @param string|null $path
     * @return string|null
     */
    public function getUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::url($path);
    }
}
