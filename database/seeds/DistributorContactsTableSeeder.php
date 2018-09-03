<?php

use Illuminate\Database\Seeder;

use App\DistributorContact;

class DistributorContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('distributor_contacts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DistributorContact::create([
        	'name' => '李四',
        	'mobile' => '18910109898',
        	'telephone' => '66668888',
        	'phone_area_code' => '010',
        	'distributor_id' => 1,
        	'default_contact' => true,
        ]);
    }
}
