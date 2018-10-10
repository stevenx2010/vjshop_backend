<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->string('description');
            $table->string('setting_name');
            $table->string('setting_value');
            $table->string('setting_value_postfix');
            $table->decimal('parameter1');
            $table->decimal('parameter2');
            $table->decimal('condition1');
            $table->decimal('condition2');
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
        Schema::dropIfExists('settings');
    }
}
