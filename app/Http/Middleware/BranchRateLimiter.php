<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;


class BranchRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $this->throttleBranchRequests($request->branch_id);

        return $next($request);
    }

    private function throttleBranchRequests($branchId)
    {
        $maxAttempts = 10; // 10 requests per minute
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts('create-order:' . $branchId, $maxAttempts)) {
            abort(429, 'Too many requests');
        }

        RateLimiter::hit('create-order:' . $branchId, $decayMinutes * 60);
    }
}
