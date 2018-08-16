<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomePageImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_page_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url', 255);
            $table->string('click_to_url', 255)->nullable();
            $table->tinyInteger('position')->unsigned();
            $table->smallInteger('width')->unsigned();
            $table->smallInteger('height')->unsigned();
            $table->tinyInteger('sort_order')->unsigned();
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
        Schema::dropIfExists('home_page_images');
    }
}
