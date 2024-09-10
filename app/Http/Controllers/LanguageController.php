<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{

    public function changeLanguage($locale)
    {
        if (array_key_exists($locale, Config::get('languages'))) {
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }
}
