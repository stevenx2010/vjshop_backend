<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('description', 600);
            $table->integer('product_sub_category_id')->unsigned();
            $table->foreign('product_sub_category_id')->references('id')->on('product_sub_categories')->delete('cascade');
            $table->string('product_sub_category_name');
            $table->string('model', 40);
            $table->string('package_unit', 4);
            $table->decimal('weight');
            $table->string('weight_unit', 6);
            $table->decimal('price');
            $table->string('brand')->default('Venjong')->nullable();
            $table->integer('inventory')->unsigned();
            $table->string('thumbnail_url', 255);
            $table->integer('sold_amount')->unsiged()->nullable()->default(0);
            $table->integer('sort_order')->unsigne();
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
        Schema::table('products', function(Blueprint $table) {
            $table->dropForeign('product_sub_category_id');
            $table->dropColumn('product_sub_category_id');
        });

        Schema::dropIfExists('products');
    }
}
