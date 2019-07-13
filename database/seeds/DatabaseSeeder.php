<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call('HomePageImagesTableSeeder');

        $this->call('ProductCategoriesTableSeeder');
        $this->call('ProductSubCategoriesTableSeeder');
        $this->call('ProductsTableSeeder');
        $this->call('ProductImagesTableSeeder');
        $this->call('DistributorsTableSeeder');
        $this->call('DistributorAddressesTableSeeder');
        $this->call('DistributorContactsTableSeeder');
        $this->call('CouponTypesTableSeeder');
        $this->call('CouponsTableSeeder');
        $this->call('CouponForNewComersTableSeeder');
        $this->call('AppVersionsTableSeeder');
        $this->call('QuestionAndAnswersTableSeeder');
        $this->call('SettingsTableSeeder');
        $this->call('UsersTableSeeder');
        $this->call('AboutsTableSeeder');
        $this->call('AboutImagesTableSeeder');
        $this->call('AgreementsTableSeeder');
    }
}
