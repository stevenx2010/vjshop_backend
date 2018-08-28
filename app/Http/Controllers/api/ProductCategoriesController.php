<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\ProductCategory;

use Illuminate\Support\Facades\Log;

class ProductCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductCategory::select('id', 'name', 'description', 'sort_order')->orderBy('sort_order')->get();
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
    public function show($productId)
    {
        return ProductCategory::select('id', 'name', 'description', 'sort_order')->where('id', $productId)->get();
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
        $ProductCategory = ProductCategory::updateOrCreate(
            ['id' => $request['id']],
            [
                'name' => $request['name'],
                'description' => $request['description'],
                'sort_order' => $request['sort_order']
            ]
        );

        return response("{'status', 1}", 200)->header('Content-type', 'application/json');
    }

    public function swap(Request $request) 
    {
        $i = (ProductCategory::select('sort_order')->where('id', $request[0])->get())[0];
        $j = (ProductCategory::select('sort_order')->where('id', $request[1])->get())[0];

        $i = (json_decode($i, true))['sort_order'];
        $j = (json_decode($j, true))['sort_order'];

        ProductCategory::find($request[0])->update(['sort_order' => $j]);
        ProductCategory::find($request[1])->update(['sort_order' => $i]);

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
        ProductCategory::destroy($productId);

        Log::debug($productId);

        return response('deleted', 200);
    }
}
