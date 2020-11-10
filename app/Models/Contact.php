<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'organization_id',
        'name',
        'country_id',
        'street',
        'city_id',
        'city_text',
        'state_id',
        'state_text',
        'zipcode_id',
        'zipcode_text',
        'contact_no',
        'phone',
        'fax',
        'currency_id',
        'registration_no',
        'ean',
        'locale_id',
        'is_customer',
        'is_supplier',
        'payment_terms_mode',
        'payment_terms_days',
        'access_code',
        'email_attachment_delivery_mode',
        'is_archived',
        'is_sales_tax_exempt',
        'default_expense_product_description',
        'default_expense_account_id',
        'default_tax_rate_id',
        'external_id'
    ];
}
