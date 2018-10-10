<?php

use Illuminate\Database\Seeder;

use App\Setting;
use App\Libraries\Utilities\SettingType;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
        	'type' => SettingType::SHIPPING_FEE_FORMULA,
        	'description' => '首重10kg免运费，超出后每公斤按照1元收取。',
        	'setting_name' => 'shipping_fee',
        	'setting_value' => '(w-m)*p',
        	'setting_value_postfix' => 'wm-p*',
        	'parameter1' => 10,
        	'parameter2' => 1.00,
        	'condition1' => 0,
        	'condition2' => 10000
        ]);
    }
}
