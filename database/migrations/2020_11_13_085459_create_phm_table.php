<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phm', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('client_id');
            $table->string('pdf_path');
            $table->string('chart1');
            $table->string('chart2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phm');
    }
}
