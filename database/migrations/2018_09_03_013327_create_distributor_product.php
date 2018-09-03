<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_product', function (Blueprint $table) {
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('distributor_id')->unsigned()->nullable();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->integer('inventory')->unsigned()->default(0);
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
        Schema::table('distributor_product', function(Blueprint $table) {
            $table->dropForeign('distributor_id');
            $table->dropColumn('distributor_id');
            $table->dropForeign('product_id');
            $table->dropColumn('product_id');
        });       
        Schema::dropIfExists('distributor_product');
    }
}
