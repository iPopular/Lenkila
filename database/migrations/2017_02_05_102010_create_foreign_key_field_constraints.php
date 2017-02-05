<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyFieldConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->foreign('field_id')->references('id')->on('field')->onDelete('cascade');
        });

        Schema::table('tmp_field_price', function (Blueprint $table) {
            $table->foreign('field_id')->references('id')->on('field')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservation', function (Blueprint $table) {
           $table->dropForeign('reservation_field_id_foreign');
        });

        Schema::table('tmp_field_price', function (Blueprint $table) {
            $table->dropForeign('tmp_field_price_field_id_foreign');
        });
    }
}
