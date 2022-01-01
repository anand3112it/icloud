<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTransdetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_transdetail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('financialtranid')->unsigned()->default('0');
            $table->tinyInteger('moduleid')->unsigned()->default('0');
            $table->decimal('amount', 12, 2);
            $table->smallInteger('headid')->unsigned()->default('0');
            $table->string('crdr', 20);
            $table->tinyInteger('br_id')->unsigned()->default('0');
            $table->string('head_name', 100);
            $table->text('csv_record_details')->comment('Original csv record details in json');
            $table->foreign('financialtranid')->references('id')->on('financial_trans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_transdetail');
    }
}
