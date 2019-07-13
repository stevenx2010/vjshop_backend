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
            $table->foreign('product_sub_category_id')->references('id')->on('product_sub_categories')->onDelete('cascade');
            $table->string('product_sub_category_name');
            $table->string('model', 40);
            $table->string('package_unit', 4);
            $table->decimal('weight');
            $table->string('weight_unit', 6);
            $table->decimal('price');
            $table->string('brand')->default('稳卓')->nullable();
            $table->integer('inventory')->unsigned();
            $table->string('thumbnail_url', 255);
            $table->integer('sold_amount')->unsiged()->nullable()->default(0);
            $table->boolean('off_shelf')->default(false);
            $table->integer('sort_order')->unsigned();
            $table->string('package')->default('盒装')->nullable();
            $table->string('coating')->default('镀锌')->nullable();
            $table->string('quality')->default('售后')->nullable();
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
            $table->dropForeign('package_id');
            $table->dropColumn('package_id');
            $table->dropForeign('coating_id');
            $table->dropColumn('coating_id');
            $table->dropForeign('quality_id');
            $table->dropColumn('quality_id');
        });

        Schema::dropIfExists('products');
    }
}
