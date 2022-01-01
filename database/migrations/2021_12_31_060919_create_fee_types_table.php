<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->tinyInteger('fee_category')->unsigned()->default('0');
            $table->string('f_name', 100);
            $table->tinyInteger('collection_id')->unsigned()->default('0');
            $table->tinyInteger('br_id')->unsigned()->default('0');
            $table->tinyInteger('seq_id')->unsigned()->default('0');
            $table->string('fee_type_ledger', 255);
            $table->tinyInteger('fee_headtype')->unsigned()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_types');
    }
}
