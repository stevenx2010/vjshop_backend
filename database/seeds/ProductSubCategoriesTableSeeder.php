<?php

use Illuminate\Database\Seeder;

use App\ProductSubCategory;

class ProductSubCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('product_sub_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        ProductSubCategory::create([
        	'name' =>'单盒平衡块',
        	'description' => 'Single Package of Balancers',
        	'product_category_id' => 2,
        	'sort_order' => 1
        ]);

        ProductSubCategory::create([
        	'name' =>'平衡块全季套装',
        	'description' => 'Packages of Balancers for all seasons',
        	'product_category_id' => 2,
        	'sort_order' => 2
        ]);

        ProductSubCategory::create([
        	'name' =>'平衡块冬季套装',
        	'description' => 'Packages of Balancers for winter',
        	'product_category_id' => 2,
        	'sort_order' => 3
        ]);

        ProductSubCategory::create([
        	'name' =>'TPMS胎压监测',
        	'description' => 'TPMS',
        	'product_category_id' => 3,
        	'sort_order' => 1
        ]);

        ProductSubCategory::create([
        	'name' =>'平衡块工具',
        	'description' => 'Tools for Balancers',
        	'product_category_id' => 4,
        	'sort_order' => 1
        ]);
    }
}
