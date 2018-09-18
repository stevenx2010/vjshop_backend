<?php

use Illuminate\Database\Seeder;

use App\DistributorAddress;

class DistributorAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('distributor_addresses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DistributorAddress::create([
        	'distributor_id' => 1,
        	'city' => '北京市 市辖区 东城区',
        	'street' => '东单甲一号副288号',
        	'default_address' => true,
        ]);

        DistributorAddress::create([
        	'distributor_id' => 1,
        	'city' => '北京市 市辖区 丰台区',
        	'street' => '马家楼3122号',
        	'default_address' => false,
        ]);
    }
}
