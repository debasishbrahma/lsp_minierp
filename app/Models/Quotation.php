<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['customer_name', 'user_id', 'total_price', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    // Cache a single quotation
    public static function getCached($id)
    {
        $cacheKey = 'quotation_' . $id;
        Log::debug('Attempting to fetch quotation from Redis cache', ['cache_key' => $cacheKey]);

        return Cache::store('redis')->remember($cacheKey, now()->addMinutes(60), function () use ($id) {
            Log::debug('Redis cache miss for quotation, loading from DB', ['quotation_id' => $id]);
            return self::with('items', 'user')->findOrFail($id);
        });
    }

    // Clear cache for a single quotation
    public static function clearCache($id)
    {
        Cache::store('redis')->forget('quotation_' . $id);
        Log::debug('Quotation cache manually cleared', ['quotation_id' => $id]);
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($quotation) {
            Cache::store('redis')->forget('quotations_user_' . $quotation->user_id);
            Log::debug('Quotation cache cleared', ['quotation_id' => $quotation->id]);
        });

        static::deleted(function ($quotation) {
            Cache::store('redis')->forget('quotations_user_' . $quotation->user_id);
            Log::debug('Quotation soft deleted, cache cleared', ['quotation_id' => $quotation->id]);
        });

        static::created(function ($quotation) {
            Cache::store('redis')->forget('quotations_user_' . auth()->id());
            Log::debug('Quotation created, cache cleared', ['quotation_id' => $quotation->id]);
        });
    }
}
