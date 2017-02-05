<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpCustomerStadiumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_customer_stadium', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stadium_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->string('member_id', 10);
            $table->string('note', 300)->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_customer_stadium');
    }
}
