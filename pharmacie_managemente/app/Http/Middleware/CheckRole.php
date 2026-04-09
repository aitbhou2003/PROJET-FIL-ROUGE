<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();

        if ($user->role->role !== $role) {
            abort(403, "acces intedrdit , vous n'avez pas la permissions nexessaires");
        }

        return $next($request);
    }
}
