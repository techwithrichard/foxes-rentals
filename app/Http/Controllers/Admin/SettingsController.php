<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view settings');


    }

    public function index()
    {

        return view('admin.settings.global');

    }

    public function appearance()
    {
        return view('admin.settings.appearance');

    }

    public function house_types()
    {

        return view('admin.settings.house_types');

    }

    public function property_types()
    {
        return view('admin.settings.property_types');
    }

    public function payment_methods()
    {
        return view('admin.settings.payment_methods');
    }

    public function company_settings()
    {
        return view('admin.settings.company_settings');
    }

    public function expense_types()
    {
        return view('admin.settings.expense_types');
    }
}
