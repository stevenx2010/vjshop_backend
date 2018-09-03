<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->char('mobile', 11);
            $table->char('telephone', 8);
            $table->char('phone_area_code', 4);
            $table->boolean('default_contact')->default(false);
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
        Schema::table('distributor_contacts', function(Blueprint $table) {
            $table->dropForeign('distributor_id');
            $table->dropColumn('distributor_id');
        });
        
        Schema::dropIfExists('distributor_contacts');
    }
}
