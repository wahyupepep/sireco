<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOnTableAbouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abouts', function (Blueprint $table) {
            $table->dropColumn('keywords');
            $table->string('owner')->after('copyright');
            $table->text('owner_photo')->after('owner');
            $table->text('owner_statement')->after('owner_photo');
            $table->text('image_url')->after('owner_statement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('abouts', function (Blueprint $table) {
            $table->dropColumn(['owner', 'owner_photo', 'owner_statement', 'image_url']);
        });
    }
}
