<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogCsrfAndSessionErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Logger les informations de la requête entrante
        $this->logRequestInfo($request);

        // Exécuter la requête
        $response = $next($request);

        // Logger les informations de la réponse
        $this->logResponseInfo($request, $response);

        return $response;
    }

    /**
     * Logger les informations détaillées de la requête
     */
    protected function logRequestInfo(Request $request): void
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_ajax' => $request->ajax() || $request->expectsJson(),
            'has_csrf_token' => $request->hasSession() && $request->session()->has('_token'),
            'csrf_token_from_header' => $request->header('X-CSRF-TOKEN'),
            'csrf_token_input' => $request->input('_token'),
            'session_id' => $request->session()->getId(),
            'session_authenticated' => $request->session()->has('login_web_' . sha1(config('app.key'))),
            'user_id' => auth()->id(),
            'user_authenticated' => auth()->check(),
        ];

        // Informations de session supplémentaires
        if ($request->hasSession()) {
            $logData['session_last_activity'] = $request->session()->get('last_activity');
            $logData['session_created_at'] = $request->session()->get('created_at');
        }

        Log::channel('stack')->info('CSRF/Session Request Info', $logData);

        // Alertes spécifiques
        if (!auth()->check() && !$request->is('login', 'register')) {
            Log::channel('stack')->warning('Non-authenticated request to protected route', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'method' => $request->method(),
                'referer' => $request->header('referer'),
            ]);
        }

        if ($request->isMethod('POST') && !$request->header('X-CSRF-TOKEN') && !$request->input('_token')) {
            Log::channel('stack')->warning('POST request without CSRF token', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'has_session' => $request->hasSession(),
            ]);
        }
    }

    /**
     * Logger les informations de la réponse
     */
    protected function logResponseInfo(Request $request, Response $response): void
    {
        $statusCode = $response->getStatusCode();

        // Logger les réponses d'erreur
        if ($statusCode >= 400) {
            $logData = [
                'status_code' => $statusCode,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'is_redirect' => $response->isRedirect(),
                'redirect_target' => $response->headers->get('Location'),
                'has_csrf_token_mismatch' => $statusCode === 419,
            ];

            Log::channel('stack')->error('HTTP Error Response', $logData);

            // Alertes spécifiques pour les codes d'erreur courants
            match ($statusCode) {
                401 => Log::channel('stack')->error('Unauthorized - Authentication required', $logData),
                419 => Log::channel('stack')->error('CSRF Token Mismatch - Page expired', [
                    ...$logData,
                    'session_id' => $request->session()->getId(),
                    'session_lifetime' => config('session.lifetime'),
                ]),
                422 => Log::channel('stack')->warning('Validation Error', $logData),
                default => null,
            };
        }

        // Logger les redirections vers login (session expirée)
        if ($response->isRedirect() && str_contains($response->headers->get('Location') ?? '', 'login')) {
            Log::channel('stack')->warning('Redirect to login detected - Session likely expired', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : 'no session',
                'user_was_authenticated' => auth()->check(),
                'redirect_location' => $response->headers->get('Location'),
                'referer' => $request->header('referer'),
            ]);
        }
    }

    /**
     * Terminer la requête avec des logs supplémentaires
     */
    public function terminate(Request $request, Response $response): void
    {
        // Logger la fin de traitement pour les requêtes POST longues
        if ($request->isMethod('POST')) {
            Log::channel('stack')->debug('POST Request completed', [
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : 'no session',
            ]);
        }
    }
}
