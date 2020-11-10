<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MofidyProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('prices');
            $table->string('is_in_inventory')->nullable();
            $table->string('image_id')->nullable();
            $table->string('image_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('prices')->nullable();
            $table->dropColumn('is_in_inventory');
            $table->dropColumn('image_id');
            $table->dropColumn('image_url');
        });
    }
}
