<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyStadiumConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('stadium_id')->references('id')->on('stadium')->onDelete('cascade');
        });

        Schema::table('field', function (Blueprint $table) {
            $table->foreign('stadium_id')->references('id')->on('stadium')->onDelete('cascade');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->foreign('stadium_id')->references('id')->on('stadium')->onDelete('cascade');
        });

        Schema::table('tmp_customer_stadium', function (Blueprint $table) {
            $table->foreign('stadium_id')->references('id')->on('stadium')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table)
        {
            $table->dropForeign('users_stadium_id_foreign');
        });

        Schema::table('field', function ($table)
        {
            $table->dropForeign('field_stadium_id_foreign');
        });

        Schema::table('promotions', function ($table)
        {
            $table->dropForeign('promotions_stadium_id_foreign');
        });

        Schema::table('tmp_customer_stadium', function ($table)
        {
            $table->dropForeign('tmp_customer_stadium_stadium_id_foreign');
        });
    }
}
