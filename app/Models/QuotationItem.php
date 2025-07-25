<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['quotation_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
