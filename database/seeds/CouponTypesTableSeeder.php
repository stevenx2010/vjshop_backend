<?php

use Illuminate\Database\Seeder;

use App\CouponType;

class CouponTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('coupon_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        CouponType::create([
        	'type' => '全场通用券',
        	'description' => '本优惠券可以在任何商品中使用',
        	'sort_order' => 10
        ]);

        CouponType::create([
        	'type' => '中秋特惠券',
        	'description' => '本优惠券可在中秋节前后5天使用',
        	'sort_order' => 20
        ]);

        CouponType::create([
        	'type' => '新人礼券',
        	'description' => '对每个新注册的用户发放本优惠券',
        	'sort_order' => 30
        ]);
    }
}
