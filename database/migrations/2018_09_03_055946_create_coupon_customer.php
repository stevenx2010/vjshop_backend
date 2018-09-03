<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_customer', function (Blueprint $table) {
            $table->integer('coupon_id')->unsigned()->nulalble();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->integer('quantity')->unsigned()->default(0);
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
        Schema::table('coupon_customer', function(Blueprint $table) {
            $table->dropForeign('coupon_id');
            $table->dropColumn('coupon_id');
            $table->dropForeign('customer_id');
            $table->dropColumn('customer_id');
        });


        Schema::dropIfExists('coupon_customer');
    }
}
