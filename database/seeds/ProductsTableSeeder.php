<?php

use Illuminate\Database\Seeder;

use App\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Product::create([
        	'name' => 'V350-100经典牛皮纸胶带平衡块',
        	'description' => "采用原装德国工艺制造，原材料均为进口。表面镀锡，重量准确。每盒100条装：5g 20条，10g 80条。",
        	'product_sub_category_id' => 1,
            'product_sub_category_name' => '单盒平衡块',
        	'model' => 'V350-100',
        	'package_unit' => '盒',
        	'weight' => 6,
        	'weight_unit' => 'kg',
        	'price' => 90.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v350-100.png',
            'saled_amount' => 1,
        	'sort_order' => 1
        ]);

         Product::create([
        	'name' => 'V350-WS冬季增强型胶带+高防腐表面处理平衡块',
        	'description' => '采用原装德国工艺制造，原材料均为进口。表面高防腐处理，重量准确。每盒50条装：5g 20条，10g 30条。',
        	'product_sub_category_id' => 1,
            'product_sub_category_name' => '单盒平衡块',
        	'model' => 'V350-WS',
        	'package_unit' => '盒',
        	'weight' => 6,
        	'weight_unit' => 'kg',
        	'price' => 110.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v350-ws.png',
            'saled_amount' => 0,
        	'sort_order' => 2
        ]);    

        Product::create([
        	'name' => 'V350经典牛皮纸胶带平衡块',
        	'description' => '采用原装德国工艺制造，原材料均为进口。表面镀锡，重量准确。每盒50条装：5g 20条，10g 30条。',
        	'product_sub_category_id' => 1,
            'product_sub_category_name' => '单盒平衡块',
        	'model' => 'V350',
        	'package_unit' => '盒',
        	'weight' => 6,
        	'weight_unit' => 'kg',
        	'price' => 90.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v350.png',
            'saled_amount' => 0,
        	'sort_order' => 3
        ]);  

        Product::create([
        	'name' => 'V350-W100冬季增强型胶带平衡块',
        	'description' => '采用原装德国工艺制造，原材料均为进口。表面镀锡，重量准确。每盒100条装：5g 20条，10g 80条。',
        	'product_sub_category_id' => 1,
            'product_sub_category_name' => '单盒平衡块',
        	'model' => 'V350-W100',
        	'package_unit' => '盒',
        	'weight' => 6,
        	'weight_unit' => 'kg',
        	'price' => 90.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v350-w100.png',
            'saled_amount' => 0,
        	'sort_order' => 4
        ]); 

        Product::create([
        	'name' => 'V355牛皮纸胶带平衡块',
        	'description' => '采用原装德国工艺制造，原材料均为进口。表面镀锡，重量准确。每盒100条装：5g 20条，10g 80条。',
        	'product_sub_category_id' => 1,
            'product_sub_category_name' => '单盒平衡块',
        	'model' => 'V355',
        	'package_unit' => '盒',
        	'weight' => 6,
        	'weight_unit' => 'kg',
        	'price' => 90.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v355.png',
        	'sort_order' => 5
        ]);

         /*************************************************************/
        Product::create([
        	'name' => 'V01全季精品平衡块套装',
        	'description' => '本精品套装包含V350-100经典牛皮纸胶带平衡块4盒，V350-WS冬季增强型胶带+高防腐表面处理平衡块2盒。',
        	'product_sub_category_id' => 2,
            'product_sub_category_name' => '平衡块全季套装',
        	'model' => 'V01全季',
        	'package_unit' => '套',
        	'weight' => 30,
        	'weight_unit' => 'kg',
        	'price' => 485.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v01.png',
        	'sort_order' => 1
        ]);

        Product::create([
        	'name' => 'V02全季精品平衡块套装',
        	'description' => '本精品套装包含V350-100经典牛皮纸胶带平衡块4盒，V355 5g+10g牛皮纸胶带平衡块2盒。',
        	'product_sub_category_id' => 2,
            'product_sub_category_name' => '平衡块全季套装',
        	'model' => 'V02全季',
        	'package_unit' => '套',
        	'weight' => 30,
        	'weight_unit' => 'kg',
        	'price' => 475.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v02.png',
        	'sort_order' => 1
        ]);

        Product::create([
        	'name' => 'V03全季精品平衡块套装',
        	'description' => '本精品套装包含V350-100经典牛皮纸胶带平衡块10盒。',
        	'product_sub_category_id' => 2,
            'product_sub_category_name' => '平衡块全季套装',
        	'model' => 'V03全季',
        	'package_unit' => '套',
        	'weight' => 30,
        	'weight_unit' => 'kg',
        	'price' => 480.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/v03.png',
        	'sort_order' => 1
        ]);

         /*************************************************************/
        Product::create([
        	'name' => 'W01冬季精品平衡块套装',
        	'description' => '本精品套装包含V350-W100增强型胶带平衡块4盒，V350-WS冬季增强型胶带+高防腐表面处理平衡块2盒。',
        	'product_sub_category_id' => 3,
            'product_sub_category_name' => '平衡块冬季套装',
        	'model' => 'W01冬季',
        	'package_unit' => '套',
        	'weight' => 30,
        	'weight_unit' => 'kg',
        	'price' => 485.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/w01.png',
        	'sort_order' => 1
        ]);

        Product::create([
        	'name' => 'W02冬季精品平衡块套装',
        	'description' => '本精品套装包含V350-W100增强型胶带平衡块4盒，W350-WS冬季增强型胶带平衡块2盒。',
        	'product_sub_category_id' => 3,
            'product_sub_category_name' => '平衡块冬季套装',
        	'model' => 'W02冬季',
        	'package_unit' => '套',
        	'weight' => 30,
        	'weight_unit' => 'kg',
        	'price' => 475.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/w02.png',
        	'sort_order' => 1
        ]);

        Product::create([
        	'name' => 'W03冬季精品平衡块套装',
        	'description' => '本精品套装包含V350W冬季增强型胶带平衡块10盒。',
        	'product_sub_category_id' => 3,
            'product_sub_category_name' => '平衡块冬季套装',
        	'model' => 'W03冬季',
        	'package_unit' => '套',
        	'weight' => 30,
        	'weight_unit' => 'kg',
        	'price' => 480.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/w03.png',
        	'sort_order' => 1
        ]);

         /*************************************************************/
        Product::create([
        	'name' => 'T-Pro“大眼怪”系列TPMS系统',
        	'description' => '本胎压监测系统同时显示四轮胎的胎压胎温，实时无限传输监测数据，高清绿色大屏，无惧强光直射，任意摆放。特别设计按键隐藏功能，防止误操作。',
        	'product_sub_category_id' => 4,
            'product_sub_category_name' => 'TPMS胎压监测',
        	'model' => 'T-Pro系列',
        	'package_unit' => '套',
        	'weight' => 0.5,
        	'weight_unit' => 'kg',
        	'price' => 520.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/t-pro.png',
        	'sort_order' => 1
        ]);

        Product::create([
        	'name' => 'T-Smart“小精灵”系列TPMS系统',
        	'description' => '本胎压监测系统同时显示四轮胎的胎压胎温，实时无限传输监测数据，外观小巧时尚，无惧强光直射，任意摆放。特别设计按键隐藏功能，防止误操作。',
        	'product_sub_category_id' => 4,
            'product_sub_category_name' => '平衡块工具',
        	'model' => 'T-Smart系列',
        	'package_unit' => '套',
        	'weight' => 0.4,
        	'weight_unit' => 'kg',
        	'price' => 380.0,
        	'brand' => 'Venjong',
        	'inventory' => 1000,
        	'thumbnail_url' => 'imgs/t-smart.png',
        	'sort_order' => 1
        ]);

    }
}
