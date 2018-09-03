<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city');
            $table->string('street');
            $table->boolean('default_address')->default(false);
            $table->integer('distributor_id')->unsigned();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
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
        Schema::table('distributor_addresses', function(Blueprint $table) {
            $table->dropForeign('distributor_id');
            $table->dropColumn('distributor_id');
        });
        
        Schema::dropIfExists('distributor_addresses');
    }
}
