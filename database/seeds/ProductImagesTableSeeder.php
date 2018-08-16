<?php

use Illuminate\Database\Seeder;

use App\ProductImage;

class ProductImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('product_images')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');        

        ProductImage::create([
        	'product_id' => 1,
        	'image_url' => 'imgs/v350-100.jpg',
        	'position' => 1
        ])
    }
}
