<?php

use Illuminate\Database\Seeder;

use App\Coupon;

class CouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('coupons')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Coupon::create([
        	'name' => '元优惠券',
        	'description' => '从购买金额中减去40元人民币',
        	'coupon_type_id' => 1,
        	'expire_date' => '2018-09-30',
        	'expired' => false,
        	'discount_method' => 2,
        	'discount_value' => 40.00,
        	'quantity_initial' => 20,
        	'quantity_available' => 20,
        	'image_url' => 'imgs/coupon-background.png'
        ]);

        Coupon::create([
        	'name' => '元优惠券',
        	'description' => '从购买金额中减去40元人民币',
        	'coupon_type_id' => 1,
        	'expire_date' => '2018-09-30',
        	'expired' => false,
        	'discount_method' => 2,
        	'discount_value' => 40.00,
        	'quantity_initial' => 20,
        	'quantity_available' => 20,
        	'image_url' => 'imgs/coupon-background.png'
        ]);

        Coupon::create([
        	'name' => '元优惠券',
        	'description' => '从购买金额中减去40元人民币',
        	'coupon_type_id' => 2,
        	'expire_date' => '2018-09-30',
        	'expired' => false,
        	'discount_method' => 2,
        	'discount_value' => 40.00,
        	'quantity_initial' => 20,
        	'quantity_available' => 20,
        	'image_url' => 'imgs/coupon-background.png'
        ]);

        Coupon::create([
        	'name' => '折优惠券',
        	'description' => '购买金额8折优惠',
        	'coupon_type_id' => 3,
        	'expire_date' => '2018-09-30',
        	'expired' => false,
        	'discount_method' => 1,
        	'discount_value' => 0,
        	'discount_percentage' => 80,
        	'quantity_initial' => 20,
        	'quantity_available' => 20,
        	'image_url' => 'imgs/coupon-background.png'
        ]);
    }
}
