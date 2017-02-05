<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyCustomerConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
        });

        Schema::table('tmp_customer_stadium', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
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
            $table->dropForeign('reservation_customer_id_foreign');
        });

        Schema::table('tmp_customer_stadium', function (Blueprint $table) {
            $table->dropForeign('tmp_customer_stadium_customer_id_foreign');
        });
    }
}
