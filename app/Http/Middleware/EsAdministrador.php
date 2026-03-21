<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EsAdministrador
{
    /**
     * Verifica que el usuario autenticado tenga rol de SuperAdmin (1) o Admin (2).
     * Equivale al bloque: if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != '1' && $_SESSION['rol_id'] != '2')
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !in_array(Auth::user()->rol_id, [1, 2])) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
