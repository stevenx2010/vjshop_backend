<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('register_location', 100)->nullable();
            $table->string('imei', 15)->nullable();
            $table->string('ip', 15)->nullable();
            $table->boolean('gender')->nullable();
            $table->tinyInteger('age')->nullable();
            $table->double('salary')->nullable();
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
        Schema::table('customer_profiles', function(Blueprint $table) {
            $table->dropForeign('customer_id');
            $table->dropColumn('customer_id');
        });

        Schema::dropIfExists('customer_profiles');
    }
}
