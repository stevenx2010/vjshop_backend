<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\ProductImage;
use App\ProductSubCategory;
use App\ProductCategory;

use Illuminate\Support\Facades\Log;

use App\Libraries\Utilities\ProductProperty;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id', 'product_sub_category_id', 'product_sub_category_name', 'model', 'thumbnail_url', 'price', 'sold_amount', 'weight', 'sort_order')->where('off_shelf', 0)->orderBy('sort_order')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function storeProductCategory(Request $request) {
        
    }
    /**
     * Display products.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // Get product detail
    public function show($productId)
    {
        return Product::select('id', 'name', 'description', 'price', 'weight', 'weight_unit', 'sold_amount', 'weight', 'weight_unit', 'product_sub_category_name', 'product_sub_category_name', 'thumbnail_url')->where('id', $productId)->get();
    }

    public function showImages($productId, $position)
    {
        return ProductImage::select('product_id', 'image_url', 'position')->where('product_id', $productId)->where('position', $position)->get();
    }

    public function showProductSubCategories($categoryId)
    {
        return ProductSubCategory::select('id', 'name')->where('product_category_id', $categoryId)->orderBy('sort_order')->get();

    }

    public function showProducts($productCategoryId)
    {
        //return Product::select('id', 'product_sub_category_id', 'model', 'thumbnail_url')->where('product_sub_category_id', $productSubCategoryId)->orderBy('sort_order')->get();
        return ProductCategory::find($productCategoryId)->products()->select('products.id', 'product_sub_category_id', 'product_sub_category_name', 'model', 'thumbnail_url')->orderBy('product_sub_category_id')->orderBy('products.id')->get();
    }

    public function showProductsV2($productCategoryId)
    {
        $sub_categories = ProductCategory::find($productCategoryId)->productSubCategories()->orderBy('product_sub_categories.sort_order')->get();
        
        $resp = [];
        foreach($sub_categories as $subcat){
            
            $subcat_obj = ProductSubCategory::find($subcat['id']);
            $products = $subcat_obj->products()->where('products.off_shelf', 0)->orderBy('products.sort_order')->get();

            Log::debug($products);

            $productBySubCategory = new ProductBySubCategory();

            $productBySubCategory->subCategory = (ProductSubCategory::where('id', $subcat['id'])->get())[0];
            $productBySubCategory->products = $products;

            array_push($resp, $productBySubCategory);
        }

        return json_encode($resp);
    }

    public function showProductsFiltered(Request $request)
    {
        Log::debug($request);

        $products = Product::select('*')->orderBy('sort_order')->where('off_shelf', 0)->get();

        if($request['brand_vj']) $products = $products->where('brand', ProductProperty::BRAND_VJ);
        if($request['brand_hf']) $products = $products->where('brand', ProductProperty::BRAND_HF);

        if($request['package_box']) $products = $products->where('package', ProductProperty::PACKAGE_BOX);
        if($request['package_pan']) $products = $products->where('package', ProductProperty::PACKAGE_PAN);
        if($request['package_dai']) $products = $products->where('package', ProductProperty::PACKAGE_DAI);

        if($request['coating_zinc']) $products = $products->where('coating', ProductProperty::COATING_ZINC);
        if($request['coating_color']) $products = $products->where('coating', ProductProperty::COATING_COLOR);

        if($request['quality_aftermarket']) $products = $products->where('quality', ProductProperty::QUALITY_AFTERMARKET);
        if($request['quality_oem']) $products = $products->where('quality', ProductProperty::QUALITY_OEM);

        $resp = [];
        foreach($products as $p) {
            array_push($resp, $p);
        }
 
        return json_encode($resp);
    }

    public function showProductSearched($keyword) 
    {
        return Product::select('id', 'name', 'description', 'price', 'weight', 'weight_unit', 'sold_amount', 'thumbnail_url')->where('name', 'LIKE', "%{$keyword}%")->where('off_shelf', 0)->orderBy('sort_order')->get();
    }

    public function showProductsByIds(Request $request)
    {
        Log::debug($request);
        $req = $request->json()->all();
        Log::debug($req);
        $products = [];

        foreach ($req as $key) {
          //  if($key['selected']) {
                $productId = $key['productId'];
              /*  
                $product = Product::select('id', 'name', 'product_sub_category_id', 'product_sub_category_name', 'model', 'thumbnail_url','price', 'sold_amount', 'weight')->where('id', $productId)->get();*/

                $product = Product::where('id', $productId)->get();

                array_push($products, $product);
         //   }
        }

        Log::debug($products);
    
       // return response(json_encode($products), 200)->header('Content-type', 'application/json');
        return $products;
    }

    public function showProductsBySubCategoryId($productSubCategoryId) 
    {
        return Product::select('id', 'name', 'product_sub_category_id', 'product_sub_category_name', 'description', 'model', 'price', 'weight', 'brand', 'package', 'coating', 'quality', 'inventory', 'weight_unit', 'sold_amount', 'thumbnail_url', 'sort_order', 'off_shelf')->where('product_sub_category_id', $productSubCategoryId)->orderBy('sort_order')->get();
    }

    public function showByKeywordSubCatId($keyword, $subCatId) 
    {

        if($keyword == '*' || $keyword == '') {
            return Product::where('product_sub_category_id', $subCatId)->orderBy('sort_order')->get();
        }
        else {
            return Product::where('name', 'like', '%' . $keyword . '%')->where('product_sub_category_id', $subCatId)->orderBy('sort_order')->get();
        }
    }

    public function showByKeywordCatId($keyword, $catId)
    {
        if($keyword == '*' || $keyword == '') {
            return ProductCategory::find($catId)->products()->orderBy('sort_order')->get();
        } else {
            return ProductCategory::find($catId)->products()->where('products.name', 'like', '%' . $keyword . '%')->orderBy('sort_order')->get();
        }
    }

    public function showByCatId($catId)
    {

        $cat = ProductCategory::find($catId);
        return json_encode($cat->products()->orderBy('sort_order')->get());
    }

    public function showAll() {
        return Product::select('*')->orderBy('sort_order')->get();
    }

    public function showByKeyword($keyword) {
        if($keyword == '*' || $keyword == '') {
            return Product::all();
        } else {
            return Product::where('name', 'like', '%' . $keyword . '%')->orderBy('sort_order')->get();
        }
    }

    public function showByProductId($productId) {
        return Product::where('id', $productId)->get();
    }

    public function showImagesByProductId($productId) {
        return ProductImage::where('product_id', $productId)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request)
    {
        Log::debug($request);

        $numOfTopImages = $request['numOfTopImages'];
        $numOfBottomImages = $request['numOfBottomImages'];

        for($i = 0; $i < $numOfTopImages; $i++) {
            $filename = 'topImage' . $i;
            if($request->hasFile($filename)) {
                $file = $request->file($filename);
          //      $path = $file->store('public/images');
                $file->move(base_path('public/imgs'), $file->hashName());
                //Log::debug($path);
                Log::debug(base_path('public/imgs'));
            }
        }
 
        for($i = 0; $i < $numOfBottomImages; $i++) {
            $filename = 'bottomImage' . $i;
            if($request->hasFile($filename)) {
                $request->file($filename)->store('public/images');
            }
        }

        if($request->hasFile('thumbnail')) {
            $request->file('thumbnail')->store('/images', 'public');
        }

    }

    public function update(Request $request)
    {
         Log::debug($request);

        // Step 1: fill product info uploaded
        // 1) get the last sort order number
        $sort_order = $request['sort_order'];
        if($sort_order == null || $sort_order == 999) {
            $temp = Product::select('sort_order')->orderBy('sort_order', 'desc')->take(1)->get();
            $sort_order = ((json_decode($temp, true))[0])['sort_order'];
            $sort_order += 10;  
        }
   
        // 2) create thumbnail image url
        $thumbnail_url = $request['thumbnail_url'];
        if($request->hasFile('thumbnail') ) {
            $file = $request->file('thumbnail');
            //$file->store('/images', 'public');
            $hashName = $file->hashName();

            if($file->getMimeType() == 'image/jpeg')
                 $hashName = substr_replace($hashName, 'jp', -4, -1);

            $thumbnail_url = 'imgs/' . $hashName;
            $file->move(base_path('public/imgs'), $hashName);
        } 

        Log::debug($thumbnail_url);
        
        // 3) fill the product basic info & create/update it
        $product = Product::updateOrCreate(
            ['id' => $request['id'], 'product_sub_category_id' => $request['product_sub_category_id'],  'product_sub_category_name' => $request['product_sub_category_name']],
            [
             'product_sub_category_id' => $request['product_sub_category_id'],
             'product_sub_category_name' => $request['product_sub_category_name'],
             'name' => $request['name'],
             'description' => $request['description'],
             'model' => $request['model'],
            //package_unit' => $request['package_unit'],
             'weight'=> $request['weight'],
             'weight_unit' => $request['weight_unit'],
             'price' => $request['price'],
             'brand' => $request['brand'],
             'package' => $request['package'],
             'coating' => $request['coating'],
             'quality' => $request['quality'],
             'inventory' => $request['inventory'],
             'sort_order' => $sort_order,
             'thumbnail_url' => $thumbnail_url,
             'off_shelf' => $request['off_shelf'],
            ]    
        );

        // 4) get the product id of the above created/updated product info
        Log::debug($product);

        $temp = json_decode($product, true);
        $productId = $temp['id'];

        // Step 2: process uploaded product images
        // 1) images at the top
        $numOfTopImages = $request['numOfTopImages'];

        // clear top old images
        if($numOfTopImages > 0) {
            ProductImage::where('product_id', $productId)->where('position', 1)->delete();
        }

        // get sort order
        $temp = ProductImage::select('sort_order')->orderBy('sort_order', 'desc')->take(1)->get();
        $sort_order = ((json_decode($temp, true))[0])['sort_order'];
        if($sort_order == 999) $sort_order += 10;     

        for($i = 0; $i < $numOfTopImages; $i++) {
            $filename = 'topImage' . $i;
            if($request->hasFile($filename)) {
                $file = $request->file($filename);
                $hashName = $file->hashName();
                $file->move(base_path('public/imgs'), $hashName);

                // save to database
                $image = new ProductImage;
                $image->product_id = $productId;
                $image->image_url = 'imgs/' . $hashName;
                $image->position = 1;    //top image
                $image->sort_order = $sort_order + $i * 10;

                $image->save();

            }
        }

        $sort_order = $sort_order + $numOfTopImages * 10;

        // 2) images at the bottom
        $numOfBottomImages = $request['numOfBottomImages'];

        // clear bottom old images
        if($numOfBottomImages > 0) {
            ProductImage::where('product_id', $productId)->where('position', 2)->delete();
        }

        for($i = 0; $i < $numOfBottomImages; $i++) {
            $filename = 'bottomImage' . $i;
            if($request->hasFile($filename)) {
                $file = $request->file($filename);
                $hashName = $file->hashName();
                $file->move(base_path('public/imgs'), $hashName);

                // save to database
                $image = new ProductImage;
                $image->product_id = $productId;
                $image->image_url = 'imgs/' . $hashName;
                $image->position = 2;    //bottom image
                $image->sort_order = $sort_order +$i * 10;

                $image->save();
            }
        }



        $body = ['id' => $productId];

        // return the product id
        return response(json_encode($body), 200)->header('Access-Control-Allow-Origin', '*');
    }

    public function swap(Request $request) 
    {
        $i = (Product::select('sort_order')->where('id', $request[0])->get())[0];
        $j = (Product::select('sort_order')->where('id', $request[1])->get())[0];

        $i = (json_decode($i, true))['sort_order'];
        $j = (json_decode($j, true))['sort_order'];

        Product::find($request[0])->update(['sort_order' => $j]);
        Product::find($request[1])->update(['sort_order' => $i]);

        return response('sort_order swapped', 200);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId)
    {
        Product::destroy($productId);

        return respnse('deleted', 200);
    }
}

class ProductBySubCategory {
    public $subCategory;
    public $products;
}
