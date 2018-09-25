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
//v350-100
        ProductImage::create([
        	'product_id' => 1,
        	'image_url' => 'imgs/v350-100.jpg',
        	'position' => 1,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 1,
            'image_url' => 'imgs/v350-100.jpg',
            'position' => 2,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 2,
            'image_url' => 'imgs/v350-ws.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 3,
            'image_url' => 'imgs/v350.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 4,
            'image_url' => 'imgs/v350-w100.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 5,
            'image_url' => 'imgs/v355.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);
// v01
        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-1-m.jpg',
            'position' => 1,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-2-m.jpg',
            'position' => 1,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-3-m.jpg',
            'position' => 1,
            'sort_order' => 30
        ]);

        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-4-m.jpg',
            'position' => 1,
            'sort_order' => 40
        ]);
//-----------------------------------------------------
        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-1-m.jpg',
            'position' => 2,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-2-m.jpg',
            'position' => 2,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-3-m.jpg',
            'position' => 2,
            'sort_order' => 30
        ]);

        ProductImage::create([
            'product_id' => 6,
            'image_url' => 'imgs/v01-4-m.jpg',
            'position' => 2,
            'sort_order' => 40
        ]);

// v02
        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-1-m.jpg',
            'position' => 1,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-2-m.jpg',
            'position' => 1,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-3-m.jpg',
            'position' => 1,
            'sort_order' => 30
        ]);

        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-4-m.jpg',
            'position' => 1,
            'sort_order' => 40
        ]);

//------------------------------------------------
        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-1-m.jpg',
            'position' => 2,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-2-m.jpg',
            'position' => 2,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-3-m.jpg',
            'position' => 2,
            'sort_order' => 30
        ]);

        ProductImage::create([
            'product_id' => 7,
            'image_url' => 'imgs/v02-4-m.jpg',
            'position' => 2,
            'sort_order' => 40
        ]);


//v03
        ProductImage::create([
            'product_id' => 8,
            'image_url' => 'imgs/v03-1-m.jpg',
            'position' => 1,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 8,
            'image_url' => 'imgs/v03-2-m.jpg',
            'position' => 1,
            'sort_order' => 20
        ]);
//------------------------------------------
        ProductImage::create([
            'product_id' => 8,
            'image_url' => 'imgs/v03-1-m.jpg',
            'position' => 2,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 8,
            'image_url' => 'imgs/v03-2-m.jpg',
            'position' => 2,
            'sort_order' => 20
        ]);



//w01
        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-1-m.jpg',
            'position' => 1,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-2-m.jpg',
            'position' => 1,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-3-m.jpg',
            'position' => 1,
            'sort_order' => 30
        ]);

        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-4-m.jpg',
            'position' => 1,
            'sort_order' => 40
        ]);
//-----------------------------------------------
        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-1-m.jpg',
            'position' => 2,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-2-m.jpg',
            'position' => 2,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-3-m.jpg',
            'position' => 2,
            'sort_order' => 30
        ]);

        ProductImage::create([
            'product_id' => 9,
            'image_url' => 'imgs/w01-4-m.jpg',
            'position' => 2,
            'sort_order' => 40
        ]);


//w02
        ProductImage::create([
            'product_id' => 10,
            'image_url' => 'imgs/w02-1-m.jpg',
            'position' => 1,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 10,
            'image_url' => 'imgs/w02-2-m.jpg',
            'position' => 1,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 10,
            'image_url' => 'imgs/w02-3-m.jpg',
            'position' => 1,
            'sort_order' => 30
        ]);
//----------------------------------------------
        ProductImage::create([
            'product_id' => 10,
            'image_url' => 'imgs/w02-1-m.jpg',
            'position' => 2,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 10,
            'image_url' => 'imgs/w02-2-m.jpg',
            'position' => 2,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 10,
            'image_url' => 'imgs/w02-3-m.jpg',
            'position' => 2,
            'sort_order' => 30
        ]);

//w03
        ProductImage::create([
            'product_id' => 11,
            'image_url' => 'imgs/w03-1-m.jpg',
            'position' => 1,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 11,
            'image_url' => 'imgs/w03-2-m.jpg',
            'position' => 1,
            'sort_order' => 20
        ]);
//------------------------------------------
        ProductImage::create([
            'product_id' => 11,
            'image_url' => 'imgs/w03-1-m.jpg',
            'position' => 2,
            'sort_order' => 10
        ]);

        ProductImage::create([
            'product_id' => 11,
            'image_url' => 'imgs/w03-2-m.jpg',
            'position' => 2,
            'sort_order' => 20
        ]);

        ProductImage::create([
            'product_id' => 12,
            'image_url' => 'imgs/t-pro-package.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 13,
            'image_url' => 'imgs/t-pro-package.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);

        ProductImage::create([
            'product_id' => 14,
            'image_url' => 'imgs/tool01-big.jpg',
            'position' => 1,
            'sort_order' => 1
        ]);
    }
}
