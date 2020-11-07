<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->enum('type', ['company', 'person'])->default('company');
            $table->string('organization_id');
            $table->timestamp('created_time', 0);
            $table->string('name');
            $table->string('country_id');
            $table->string('street')->nullable();
            $table->string('city_id')->nullable();
            $table->string('city_text')->nullable();
            $table->string('state_id')->nullable();
            $table->string('state_text')->nullable();
            $table->string('zipcode_id')->nullable();
            $table->string('zipcode_text')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('ean')->nullable();
            $table->string('locale_id')->nullable();
            $table->boolean('is_customer')->nullable();
            $table->boolean('is_supplier')->nullable();
            $table->string('payment_terms_mode')->nullable();
            $table->smallInteger('payment_terms_days');
            $table->string('access_code')->nullable();
            $table->string('email_attachment_delivery_mode')->nullable();
            $table->boolean('is_archived')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
