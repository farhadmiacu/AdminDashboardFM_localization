<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['slug', 'image', 'status'];

    public function translations()
    {
        return $this->hasMany(BrandTranslation::class);
    }


    public function getNameAttribute()
    {
        $translation = $this->translations()->where('locale', app()->getLocale())->first();
        return $translation ? $translation->name : null;
    }

    //Controller এ ব্যবহার করার জন্য শুধু লিখলেই হবে:
    // $product->name; call করলে Laravel automatically run করবে:: getNameAttribute()


    // Or, Get the translation for the current locale (optional)
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->hasOne(BrandTranslation::class)->where('locale', $locale);
    }
}
