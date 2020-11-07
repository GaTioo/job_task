<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('organization_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('account_id');
            $table->string('product_no')->nullable();
            $table->string('suppliers_product_no')->nullable();
            $table->string('sales_tax_ruleset_id')->default('unknown');
            $table->boolean('archived')->nullable();
            $table->string('prices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
