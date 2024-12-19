<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sub_section_title')->nullable();
            $table->string('sub_section_text',2000)->nullable();
            $table->string('sub_section_no')->nullable();
            $table->string('section_id')->nullable();
            $table->string('look_img_url')->nullable();
            $table->string('phm_id')->nullable();
            $table->string('look_id')->nullable();
            $table->string('look_name')->nullable();
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
        Schema::dropIfExists('sub_sections');
    }
}
