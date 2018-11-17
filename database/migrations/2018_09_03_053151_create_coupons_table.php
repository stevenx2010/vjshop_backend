<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('coupon_type_id')->unsigned();
            $table->foreign('coupon_type_id')->references('id')->on('coupon_types')->onDelete('cascade');
            $table->datetime('expire_date');
            $table->boolean('expired');
            $table->tinyInteger('discount_method');     // 1: discount_percentage; 2: discount_value
            $table->double('discount_percentage')->default(100);
            $table->double('discount_value')->default(0);
            $table->integer('quantity_initial')->default(-1);
            $table->integer('quantity_available')->default(-1);
            $table->decimal('min_purchased_amount')->default(0.00);
            $table->string('image_url');
            $table->boolean('for_new_comer')->default(false);
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
       Schema::table('coupons', function(Blueprint $table) {
            $table->dropForeign('coupon_type_id');
            $table->dropColumn('coupon_type_id');
        });

        Schema::dropIfExists('coupons');
    }
}
