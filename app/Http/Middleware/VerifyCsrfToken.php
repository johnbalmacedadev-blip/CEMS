<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/tools/search', // GET requests don't need CSRF for search
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // If CSRF token expired on login page, regenerate and redirect back
            if ($request->is('login') && $request->isMethod('post')) {
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your session has expired. Please try logging in again.']);
            }
            
            // For other pages, throw the exception normally
            throw $e;
        }
    }
}








