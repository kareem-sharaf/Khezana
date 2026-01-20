<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCacheHeaders
{
    public function handle(Request $request, Closure $next, int $maxAge = 300): Response
    {
        $response = $next($request);

        if ($response->isSuccessful() && $request->isMethod('GET')) {
            $content = $response->getContent();
            $etag = md5($content);
            
            $lastModified = $response->getLastModified();
            if (!$lastModified) {
                $lastModified = now();
            }

            $response->setEtag($etag);
            $response->setLastModified($lastModified);
            $response->setPublic();
            $response->setMaxAge($maxAge);
            $response->headers->addCacheControlDirective('must-revalidate', false);
            
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        return $response;
    }
}
