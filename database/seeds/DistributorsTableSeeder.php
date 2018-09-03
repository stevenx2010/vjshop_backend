<?php

use Illuminate\Database\Seeder;

use App\Distributor;

class DistributorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('distributors')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Distributor::create([
        	'name' => '北京市东城区很好用稳卓特约经销商',
        	'description' => '米其林轮胎修配店'
        ]);

    }
}
