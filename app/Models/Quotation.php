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
