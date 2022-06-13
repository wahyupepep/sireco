<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->string('number_invoice');
            $table->dateTime('order_date');
            $table->string('seat_code', 20);
            $table->tinyInteger('status')->comment('0 => Waiting, 1 => Done')->default(0);
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('user_id')->comment('for user verified by frontdesk or manager')->nullable();
            $table->text('payment_file')->nullable();
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
