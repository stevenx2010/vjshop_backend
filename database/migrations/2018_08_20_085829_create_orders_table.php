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
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->integer('distributor_id')->unsigned();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->decimal('total_price');
            $table->datetime('order_date');
            $table->datetime('delivery_date');
            $table->tinyInteger('delivery_status');
            $table->tinyInteger('payment_method');
            $table->tinyInteger('shipping_method');
            $table->decimal('shipping_charges');
            $table->integer('shipping_address_id');
            $table->string('shipping_address');
            $table->tinyInteger('order_staus');     //0: not-pay-yet; 1: payed; 2: waiting for delivery; 3: in delivery; 4: received; 5: closed; 6:commented
            $table->boolean('is_invoice_required')->default(false);
            $table->tinyInteger('invoice_status');    //0: not issued yet; 1: issued
            $table->tinyInteger('invoice_type');    //0: persona;   1: enterprise
            $table->string('invoice_head', 255)->default('个人');
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
        });
        Schema::dropIfExists('orders');
    }
}
