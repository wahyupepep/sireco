<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnConvertTimeOnTableCategoryMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_members', function (Blueprint $table) {
            $table->double('convert_time')->after('day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_members', function (Blueprint $table) {
            $table->dropColumn('convert_time');
        });
    }
}
