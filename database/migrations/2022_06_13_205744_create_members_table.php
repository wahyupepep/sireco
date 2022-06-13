<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email');
            $table->string('fullname');
            $table->date('birth_date');
            $table->text('address');
            $table->integer('work_type');
            $table->string('industry_name')->nullable();
            $table->integer('hobby');
            $table->string('phone');
            $table->integer('classification_age');
            $table->string('nik');
            $table->string('password');
            $table->unsignedBigInteger('category_member_id')->nullable();
            $table->timestamps();

            $table->foreign('category_member_id')->references('id')->on('category_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
