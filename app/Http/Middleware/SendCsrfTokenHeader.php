<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SendCsrfTokenHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ajouter le token CSRF actuel dans les headers de réponse
        // Cela permet au client de rafraîchir son token CSRF
        $response->headers->set('X-CSRF-TOKEN', csrf_token());

        return $response;
    }
}
