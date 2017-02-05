<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->dateTimeTz('start_time')->nullable();
            $table->dateTimeTz('end_time')->nullable();
            $table->time('total_time')->nullable();
            $table->integer('water_price')->nullable()->default(0);
            $table->integer('field_price')->nullable()->default(0);
            $table->integer('supplement_price')->nullable()->default(0);
            $table->integer('discount_price')->nullable()->default(0);
            $table->string('note', 200)->nullable();
            $table->string('background_color', 10)->nullable();
            $table->string('ref_code', 20)->nullable();
            $table->integer('status')->default(1)->comment('1:reserved, 2:paid, 99: cancel');
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
        Schema::dropIfExists('reservation');
    }
}
