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

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id', 'product_sub_category_id', 'model', 'thumbnail_url')->get();
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

    /**
     * Display products.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // Get product detail
    public function show($productId)
    {
        return Product::select('id', 'name', 'description', 'price', 'weight', 'weight_unit', 'saled_amount')->where('id', $productId)->get();
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

    public function showProductSearched($keyword) {
        return Product::select('id', 'name', 'description', 'price', 'weight', 'weight_unit', 'saled_amount')->where('name', 'LIKE', "%{$keyword}%")->get();
    }

    public function showProductsByIds(Request $reqeust)
    {
        $req = $reqeust->json()->all();
        $products = [];

        foreach ($req as $key) {
            $productId = $key['productId'];
            
            $product = Product::select('id', 'product_sub_category_id', 'product_sub_category_name', 'model', 'thumbnail_url')->where('id', $productId)->get();

            array_push($products, $product);

            
        }
    
        return response(json_encode($products), 200)->header('Content-type', 'application/json');
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
