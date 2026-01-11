<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_path',
    ];

    /**
     * Get the full URL of the product image.
     * Uses the ProductImageService to resolve the URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return \Illuminate\Support\Facades\Storage::url($this->image_path);
        }

        return null; // Or return a default placeholder image URL here
    }
}
