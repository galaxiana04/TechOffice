// make secureheaders middleware
<?php

use Closure;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        return $response;
    }
}
