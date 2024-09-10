<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalePropertyController extends Controller
{
    //
    public function index(){
        $saleProperties = SaleProperty::findAll();
        return view('sale_properties.index', compact('saleProperties'));
    }

    // show single sale property
    public function show($id){
        $saleProperty = SaleProperty::with('property')->findOrFail($id);
        return view('sale_properties.show', compact('saleProperty'));
    }
}
