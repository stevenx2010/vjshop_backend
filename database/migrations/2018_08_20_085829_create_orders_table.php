<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_serial', 16);
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->integer('distributor_id')->unsigned();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->decimal('total_price');
            $table->decimal('total_weight');
            $table->datetime('order_date');
            $table->datetime('delivery_date')->nullable();
            $table->datetime('delivery_confirm_date')->nullable();
            $table->tinyInteger('delivery_status');
            $table->tinyInteger('payment_method');  //1: Alipay; 2. Wechat
            $table->tinyInteger('shipping_method')->nullable();
            $table->decimal('shipping_charges')->default(0.00);
            $table->integer('shipping_address_id')->unsigned();
            $table->tinyInteger('order_status');     //1: not-pay-yet; 2: payed; 3: waiting for delivery; 4: in delivery; 5: received; 6: closed; 7:commented
            $table->tinyInteger('comment_status')->unsigned()->default(1); //1: not commented, 2: commented 
            $table->boolean('is_invoice_required')->default(false);
            $table->tinyInteger('invoice_status');    //1: not issued yet; 2: issued
            $table->tinyInteger('invoice_type');    //1: persona;   2: enterprise
            $table->string('invoice_head', 255)->nullable();
            $table->string('invoice_tax_number')->nullable();
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
        Schema::table('orders', function(Blueprint $table) {
            $table->dropForeign('customer_id');
            $table->dropColumn('customer_id');
            $table->dropForeign('distributor_id');
            $table->dropColumn('distributor_id');
        });
        Schema::dropIfExists('orders');
    }
}
