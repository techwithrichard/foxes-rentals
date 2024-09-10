<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        if (session()->has('locale') and array_key_exists(session()->get('locale'), config('languages'))) {
            App::setLocale(session()->get('locale'));
        } else { // This is optional as Laravel will automatically set the fallback language if there is none specified
            App::setLocale(config('app.fallback_locale'));
        }
        return $next($request);
    }
}
