<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DebugAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Logger les informations de la requête
        if ($request->is('dprh/facturation') && $request->isMethod('POST')) {
            Log::info('DEBUG AUTH - Requête facturation', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ajax' => $request->ajax(),
                'expects_json' => $request->expectsJson(),
                'has_session' => $request->hasSession(),
                'session_id' => $request->session()->getId(),
                'user_id' => Auth::id(),
                'authenticated' => Auth::check(),
                'csrf_token' => $request->input('_token') ? 'present' : 'missing',
                'csrf_header' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing',
            ]);
        }

        $response = $next($request);

        // Logger les informations de la réponse
        if ($request->is('dprh/facturation') && $request->isMethod('POST')) {
            Log::info('DEBUG AUTH - Réponse facturation', [
                'status' => $response->getStatusCode(),
                'user_id_after' => Auth::id(),
                'authenticated_after' => Auth::check(),
            ]);
        }

        return $response;
    }
}
