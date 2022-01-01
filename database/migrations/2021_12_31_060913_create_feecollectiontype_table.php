<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeecollectiontypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feecollectiontype', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('collectionhead', 100);
            $table->string('collectiondesc', 255);
            $table->tinyInteger('br_id')->unsigned()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feecollectiontype');
    }
}
