<?php

use Illuminate\Database\Seeder;

use App\HomePageImage;

class HomePageImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('home_page_images')->truncate();

        HomePageImage::create([
        	'image_url' => 'imgs/test01.jpg',
        	'position' => 1,
        	'width' => 960,
        	'height' => 550,
        	'sort_order' => 1
        ]);

        HomePageImage::create([
            'image_url' => 'imgs/test01-2.jpg',
            'position' => 1,
            'width' => 960,
            'height' => 550,
            'sort_order' => 2
        ]);
  
        HomePageImage::create([
        	'image_url' => 'imgs/test02.jpg',
        	'position' => 1,
        	'width' => 960,
        	'height' => 550,
        	'sort_order' => 2
        ]);
 
        HomePageImage::create([
        	'image_url' => 'imgs/test03.jpg',
        	'position' => 1,
        	'width' => 960,
        	'height' => 550,
        	'sort_order' => 3
        ]);
 

        HomePageImage::create([
            'image_url' => 'imgs/new_reg.jpg',
            'position' => 2,
            'width' => 300,
            'height' => 126,
            'sort_order' => 1
        ]);

        HomePageImage::create([
            'image_url' => 'imgs/coupon_bg.jpg',
            'position' => 3,
            'width' => 300,
            'height' => 126,
            'sort_order' => 1
        ]);

        HomePageImage::create([
            'image_url' => 'imgs/test2.jpg',
            'position' => 4,
            'width' => 1920,
            'height' => 445,
            'sort_order' => 1
        ]);

        HomePageImage::create([
            'image_url' => 'imgs/test3.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 557,
            'sort_order' => 2
        ]);

       HomePageImage::create([
            'image_url' => 'imgs/test4.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 984,
            'sort_order' => 3
        ]);

       HomePageImage::create([
            'image_url' => 'imgs/test5.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 780,
            'sort_order' => 4
        ]);

        HomePageImage::create([
            'image_url' => 'imgs/test6.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 944,
            'sort_order' => 5
        ]);      

       HomePageImage::create([
            'image_url' => 'imgs/test7.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 546,
            'sort_order' => 6
        ]);

       HomePageImage::create([
            'image_url' => 'imgs/test8.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 434,
            'sort_order' => 7
        ]);

       HomePageImage::create([
            'image_url' => 'imgs/test9.jpg',
            'position' => 4,
            'width' => 950,
            'height' => 834,
            'sort_order' => 8
        ]);
    }
}
