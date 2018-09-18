<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned();
            $table->string('comment')->nullable();
            $table->datetime('comment_date')->nullable();
            $table->string('comment_owner');
            $table->integer('prev_id')->unsigned()->default(0);
            $table->integer('next_id')->unsigned()->default(0);
            $table->string('responder')->nullable();
            $table->boolean('responsed')->default(false);
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
            $table->dropForeign('order_id');
            $table->dropColumn('order_id');
            $table->dropForeign('product_id');
            $table->dropColumn('product_id');
        });        
        Schema::dropIfExists('comments');
    }
}
