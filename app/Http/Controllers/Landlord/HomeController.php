<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\LandlordRemittance;
use App\Models\Property;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $owned_properties = Property::where('landlord_id', auth()->id())->count();
        $owned_houses = House::where('landlord_id', auth()->id())->count();
        $total_remittances = LandlordRemittance::where('landlord_id', auth()->id())->sum('amount');
        return view('landlord.home.index', compact('owned_properties', 'owned_houses','total_remittances'));
    }

    public function settings()
    {
        return view('landlord.home.settings');
    }

    public function notifications()
    {
        return view('landlord.home.notifications');
    }
}
