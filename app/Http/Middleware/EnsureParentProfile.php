<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParentProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->parentProfile()->exists()) {
            return redirect()
                ->route('parent-profile.create')
                ->with('warning', 'Complete your parent profile before managing children or appointments.');
        }

        return $next($request);
    }
}
