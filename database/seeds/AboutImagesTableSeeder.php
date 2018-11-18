<?php

use Illuminate\Database\Seeder;

use App\AboutImage;

class AboutImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('about_images')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        AboutImage::create([
        	'about_id' => 1,
        	'image_url' => 'imgs/about01.jpg'
        ]);

        AboutImage::create([
        	'about_id' => 1,
        	'image_url' => 'imgs/about02.jpg'
        ]);        
    }
}
