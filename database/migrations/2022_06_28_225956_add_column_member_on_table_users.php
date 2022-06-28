<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMemberOnTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fullname')->after('id')->nullable();
            $table->string('nik')->after('fullname')->nullable();
            $table->date('birthdate')->after('email')->nullable();
            $table->text('address')->after('birthdate')->nullable();
            $table->integer('work_type')->after('address')->nullable();
            $table->string('industry_name')->after('work_type')->nullable();
            $table->tinyInteger('hobby')->after('industry_name')->nullable();
            $table->string('phone')->after('hobby')->nullable();
            $table->tinyInteger('classification_age')->after('phone')->nullable();
            $table->unsignedBigInteger('category_member_id')->after('classification_age')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fullname', 'nik', 'birthdate', 'address', 'work_type', 'industry_name', 'hobby', 'phone', 'clasification_age', 'category_member_id']);
        });
    }
}
