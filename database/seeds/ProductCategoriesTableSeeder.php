<?php

use Illuminate\Database\Seeder;

use App\ProductCategory;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('product_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        ProductCategory::create([
        	'name' => '全部产品',
        	'description' => 'List all products from database',
        	'sort_order' => 1
        ]);

        ProductCategory::create([
        	'name' => '平衡块',
        	'description' => 'List all products from database',
        	'sort_order' => 2
        ]);

        ProductCategory::create([
        	'name' => '胎压检测',
        	'description' => 'List all products from database',
        	'sort_order' => 3
        ]);

        ProductCategory::create([
        	'name' => '辅助工具',
        	'description' => 'List all products from database',
        	'sort_order' => 4
        ]);
    }
}
