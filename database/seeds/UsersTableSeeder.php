<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        User::create([
        	'name' => 'stevenx',
        	'mobile' => '13601240582',
        	'email' => 'xiel@163.net',
        	'password' => '114a8a6c1bf52f248a8d3cf72081ccdd',
        	'api_token' => '114a8a6c1bf52f248a8d3cf72081ccdd',
            'roles' => 1,
        	'first_login' => 0,
        	'last_login' => '2018-10-10 00:00:00'
        ]);
    }
}
