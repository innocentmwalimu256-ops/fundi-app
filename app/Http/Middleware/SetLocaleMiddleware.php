<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale', config('app.locale', 'sw'));

        if (in_array($locale, ['sw', 'en'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
