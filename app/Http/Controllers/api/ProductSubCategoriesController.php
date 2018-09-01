<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\ProductSubCategory;

class ProductSubCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function showByCategoryId($categoryId)
    {
        return ProductSubCategory::select('id', 'name', 'description', 'sort_order', 'product_category_id')->where('product_category_id', $categoryId)->orderBy('sort_order')->get();
    }

    public function showBySubCategoryId($subCategoryId) {
        return ProductSubCategory::select('id', 'name', 'description', 'sort_order', 'product_category_id')->where('id', $subCategoryId)->orderBy('sort_order')->get();
    }

    public function swap(Request $request) 
    {
        $i = (ProductSubCategory::select('sort_order')->where('id', $request[0])->get())[0];
        $j = (ProductSubCategory::select('sort_order')->where('id', $request[1])->get())[0];

        $i = (json_decode($i, true))['sort_order'];
        $j = (json_decode($j, true))['sort_order'];

        ProductSubCategory::find($request[0])->update(['sort_order' => $j]);
        ProductSubCategory::find($request[1])->update(['sort_order' => $i]);

        return response('sort_order swapped', 200);
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
    public function update(Request $request)
    {
        $ProductSubCategory = ProductSubCategory::updateOrCreate(
            ['id' => $request['id']],
            [
                'name' => $request['name'],
                'description' => $request['description'],
                'sort_order' => $request['sort_order'],
                'product_category_id' => $request['product_category_id']
            ]
        );

        return response("{'status', 1}", 200)->header('Content-type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($categoryId)
    {
        ProductSubCategory::destroy($categoryId);

        return response('deleted', 200);     //
    }
}
