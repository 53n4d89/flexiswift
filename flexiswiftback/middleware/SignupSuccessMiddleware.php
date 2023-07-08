<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SignupSuccessMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $referer = $request->getHeaderLine('Referer');
        $expectedReferer = $request->getUri()->getScheme() . '://' . $request->getUri()->getAuthority() . '/signup/organization';
        $expectedRefererUser = $request->getUri()->getScheme() . '://' . $request->getUri()->getAuthority() . '/signup/user';

        if ($referer !== $expectedReferer && $referer !== $expectedRefererUser) {
            // Redirect the user to a different page or display an error message
            return $handler->handle($request->withAttribute('error', 'Invalid Referer'))->withStatus(302)->withHeader('Location', '/signin');
        }

        return $handler->handle($request);
    }
}
