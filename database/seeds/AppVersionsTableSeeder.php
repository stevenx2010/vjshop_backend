<?php

use Illuminate\Database\Seeder;

use App\AppVersion;

class AppVersionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_versions')->truncate();

        AppVersion::create([
        	'latest_version' => '1.0.0'
        ]);
    }
}
