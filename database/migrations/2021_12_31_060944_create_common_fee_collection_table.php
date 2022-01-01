<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonFeeCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_fee_collection', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('moduleid')->unsigned()->default('0');
            $table->string('transid', 255);
            $table->string('admno', 100);
            $table->string('rollno', 50);
            $table->decimal('amount', 12, 2);
            $table->tinyInteger('brid')->unsigned()->default('0');
            $table->string('acadamicyear', 20);
            $table->string('financialyear', 20);
            $table->string('displayreceiptno', 100);
            $table->tinyInteger('entrymode')->unsigned()->default('0');
            $table->timestamp('paiddate', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('common_fee_collection');
    }
}
