<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserTypeAndTermsAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->user_type !== 'provider') {
            // redirect or abort
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح لك بالدخول. (مزود خدمة فقط)'], 403);
            }
            return redirect('/')->withErrors(['unauthorized' => 'هذه الصفحة خاصة بمزودي الخدمة فقط.']);
        }
        return $next($request);
    }
}
