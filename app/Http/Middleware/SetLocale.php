<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Set default locale if none is set
            $locale = config('app.locale', 'en');
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
