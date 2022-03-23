<?php

namespace App\Http\Middleware;

use App\Helpers\Api\Response;
use Closure;
use Illuminate\Http\Request;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class CustomCheckClientCredentials extends CheckClientCredentials
{
    use Response;

    public function handle(Request $request, Closure $next, ...$scopes)
    {
        $psr = (new PsrHttpFactory(
            new ServerRequestFactory,
            new StreamFactory,
            new UploadedFileFactory,
            new ResponseFactory
        ))->createRequest($request);

        try {
            $psr = $this->server->validateauthenticatedrequest($psr);
        } catch (OAuthServerException $e) {
            return $this->setStatus(401)->fail(['message' => 'Client access token invalid']);
        }

        $this->validate($psr, $scopes);

        return $next($request);
    }
}
