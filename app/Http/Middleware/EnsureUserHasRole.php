<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (! $request->user() || ! in_array($request->user()->role, $allowedRoles, true)) {
            $required = implode(', ', $allowedRoles);
            abort(403, "Halaman ini hanya untuk peran {$required}.");
        }

        return $next($request);
    }
}