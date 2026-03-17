<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsCliente
{
    /**
     * Verifica que el usuario autenticado tenga rol de Cliente (3).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->rol_id != 3) {
            // Si es admin intentando entrar a zona cliente, redirigir al dashboard admin
            if (auth()->check() && in_array(auth()->user()->rol_id, [1, 2])) {
                return redirect()->route('admin.dashboard')->with('error', '⚠️ Acceso denegado a la zona de clientes.');
            }
            
            return redirect()->route('login');
        }

        return $next($request);
    }
}
