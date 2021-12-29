<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class PDFEditorM
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
