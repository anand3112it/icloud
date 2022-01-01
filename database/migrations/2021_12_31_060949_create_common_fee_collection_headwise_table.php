<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonFeeCollectionHeadwiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_fee_collection_headwise', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('moduleid')->unsigned()->default('0');
            $table->integer('receiptid')->unsigned()->default('0');
            $table->smallInteger('headid')->unsigned()->default('0');
            $table->string('headname', 100);
            $table->tinyInteger('br_id')->unsigned()->default('0');
            $table->decimal('amount', 12, 2);
            $table->foreign('receiptid')->references('id')->on('common_fee_collection');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('common_fee_collection_headwise');
    }
}
