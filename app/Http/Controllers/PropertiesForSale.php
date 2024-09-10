<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertiesForSale extends Controller
{
    //list all propeties for sale
    public function propertiesForSale(){
       
        
        // fetch all properties from proeprtiesforsale model/database

        // return the view with theerties = \App\PropertiesForSale::all(); fetched properties
        return view('properties_for_sale', compact('properties'));
    }
}
