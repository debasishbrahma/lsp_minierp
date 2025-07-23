<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'unit_price', 'quantity_available'];

    public static function getCached($id)
    {
        $cacheKey = 'product_' . $id;
        Log::debug('Attempting to fetch product from Redis cache', ['cache_key' => $cacheKey]);

        return Cache::store('redis')->remember($cacheKey, now()->addMinutes(60), function () use ($id) {
            Log::debug('Redis cache miss, fetching product from database', ['product_id' => $id]);
            return self::findOrFail($id);
        });
    }

    public static function clearCache(mixed $id)
    {
        Cache::store('redis')->forget('product_' . $id);
        Cache::store('redis')->forget('products_user_' . auth()->id());
        Log::debug('Product cache cleared', ['product_id' => $id]);
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($product) {
            Cache::store('redis')->forget('product_' . $product->id);
            Cache::store('redis')->forget('products_user_' . auth()->id());
            Log::debug('Product cache cleared', ['product_id' => $product->id]);
        });

        static::deleted(function ($product) {
            Cache::store('redis')->forget('product_' . $product->id);
            Cache::store('redis')->forget('products_user_' . auth()->id());
            Log::debug('Product soft deleted, cache cleared', ['product_id' => $product->id]);
        });
    }
}
