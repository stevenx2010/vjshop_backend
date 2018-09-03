<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponTypeProductSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_type_product_sub_category', function (Blueprint $table) {
            $table->integer('coupon_type_id')->unsigned()->nullable();
            $table->foreign('coupon_type_id')->references('id')->on('coupon_types')->onDelete('cascade');
            $table->integer('product_sub_category_id')->unsigned()->nullable();
            $table->foreign('product_sub_category_id')->references('id')->on('product_sub_categories')->onDelete('cascade');
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
        Schema::table('coupon_type_product_sub_category', function(Blueprint $table) {
            $table->dropForeign('coupon_id');
            $table->dropColumn('coupon_id');
            $table->dropForeign('product_sub_category_id');
            $table->dropColumn('product_sub_category_id');
        });


        Schema::dropIfExists('coupon_type_product_sub_category');
    }
}
