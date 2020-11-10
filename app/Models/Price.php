<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
    	'product_id', 'external_id', 'unit_price', 'currency_id'
    ];

    public function product()
    {
        return $this->belongsTo('\App\Models\Product', 'product_id');
    }

}
