<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Esta é a proteção real: roda no servidor em toda rota /admin
        abort_unless($request->user()?->isAdmin(), 403, 'Acesso restrito ao administrador.');

        return $next($request);
    }
}
