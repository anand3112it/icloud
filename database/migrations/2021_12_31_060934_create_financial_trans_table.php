<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_trans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('roll_no', 50);
            $table->tinyInteger('moduleid')->unsigned()->default('0');
            $table->string('tranid', 100);
            $table->decimal('amount', 12, 2);
            $table->string('crdr', 20);
            $table->date('tranDate')->nullable(true);
            $table->string('acadYear', 20);
            $table->tinyInteger('entrymode')->unsigned()->default('0');
            $table->string('voucherno', 50);
            $table->tinyInteger('brid')->unsigned()->default('0');
            $table->decimal('due_amount', 12, 2);
            $table->decimal('concession_amount', 12, 2);
            $table->decimal('duerev', 12, 2);
            $table->timestamps();
            $table->ipAddress('created_ip');
            $table->ipAddress('updated_ip');
            $table->index(['tranid'], 'IDX_TransId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_trans');
    }
}
