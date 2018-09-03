<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Distributor;
use App\DistributorAddress;
use App\DistributorContact;

class DistributorController extends Controller
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
    public function show($distributorId)
    {
        return Distributor::select('id', 'name', 'description')->where('id', $distributorId)->get();
    }

    public function showAddress($city) 
    {
        return DistributorAddress::select('distributor_id', 'city', 'street', 'default_address')->where('city', 'like', '%'. $city . '%')->where('default_address', true)->get();
    }

    public function showContact($distributorId)
    {
        return DistributorContact::select('distributor_id', 'name', 'mobile', 'phone_area_code', 'telephone', 'default_contact')->where('distributor_id', $distributorId)->where('default_contact', true)->get();
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
