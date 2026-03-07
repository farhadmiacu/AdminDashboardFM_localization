<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'code',
        'short_description',
        'long_description',
        'highlight_title',
        'regular_price',
        'selling_price',
        'image',
        'stock',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function productMultiImages()
    {
        return $this->hasMany(ProductMultiImage::class);
    }
}
