<?php

use Illuminate\Database\Seeder;

use App\CouponForNewComer;

class CouponForNewComersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coupon_for_new_comers')->truncate();

        CouponForNewComer::create([
        	'description' => '该标题可在后台修改',
        	'image_url' => 'imgs/newcomer_pic.png'
        ]);
/*
        CouponForNewComer::create([
        	'description' => 'pic01',
        	'image_url' => 'imgs/v03.jpg'
        ]);*/
    }
}
