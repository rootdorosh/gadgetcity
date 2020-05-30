<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use FrontPage;

class DetectDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        FrontPage::setLocale();
        
        return $next($request);
    }
}
