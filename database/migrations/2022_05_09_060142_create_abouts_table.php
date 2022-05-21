<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAboutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('title');
            $table->text('favicon');
            $table->text('logo');
            $table->string('email');
            $table->text('address');
            $table->string('phone_company');
            $table->string('phone_order');
            $table->string('phone_mitra');
            $table->text('txt_phone_order');
            $table->text('txt_phone_mitra');
            $table->text('url');
            $table->text('meta_title');
            $table->text('meta_description');
            $table->text('keywords');
            $table->text('url_facebook');
            $table->text('url_instagram');
            $table->text('url_whatsapp');
            $table->text('url_maps');
            $table->string('copyright');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abouts');
    }
}
