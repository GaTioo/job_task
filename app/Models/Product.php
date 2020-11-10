<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_id',
        'external_id',
        'name',
        'description',
        'account_id',
        'inventory_account_id',
        'product_no',
        'suppliers_product_no',
        'sales_tax_ruleset_id',
        'is_archived',
        'is_in_inventory',
        'image_id',
        'image_url'
    ];


    public function prices()
    {
        return $this->hasMany('\App\Models\Price');
    }

}
