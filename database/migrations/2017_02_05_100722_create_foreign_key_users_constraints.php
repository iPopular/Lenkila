<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyUsersConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('field', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('reservation', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('tmp_customer_stadium', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropForeign('customer_created_by_foreign');
            $table->dropForeign('customer_updated_by_foreign');            
        });

        Schema::table('field', function (Blueprint $table) {
            $table->dropForeign('field_created_by_foreign');
            $table->dropForeign('field_updated_by_foreign'); 
        });

        Schema::table('reservation', function (Blueprint $table) {
            $table->dropForeign('reservation_created_by_foreign');
            $table->dropForeign('reservation_updated_by_foreign'); 
        });

        Schema::table('tmp_customer_stadium', function (Blueprint $table) {
            $table->dropForeign('tmp_customer_stadium_created_by_foreign');
            $table->dropForeign('tmp_customer_stadium_updated_by_foreign'); 
        });
    }
}
