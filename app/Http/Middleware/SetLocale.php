<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'fr', 'es']; 

        $locale = $request->header('Accept-Language', $request->cookie('locale'));
        $locale = in_array($locale, $supportedLocales) ? $locale : 'en';

        app()->setLocale($locale);

        return $next($request);
    }
}
