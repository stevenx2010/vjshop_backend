<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 25);
            $table->string('mobile', 11);
            $table->string('email')->unique();
            $table->string('password', 255);
            $table->string('api_token', 250);
            $table->string('image_url')->default('imgs/user.gif');
            $table->boolean('first_login')->default(true);
            $table->datetime('last_login');
            $table->rememberToken();
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
        Schema::table('users', function (Blueprint $table){
            $table->dropColumn('api_token');
        });
        Schema::dropIfExists('users');
    }
}
