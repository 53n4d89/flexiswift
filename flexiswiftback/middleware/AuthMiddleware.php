<?php

namespace App\middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use App\signin\services\SigninService;
use Exception;

class AuthMiddleware {
    protected $signinService;

    public function __construct(SigninService $signinService) {
        $this->signinService = $signinService;
    }

    public function __invoke(Request $request, Handler $handler): Response {
        $cookies = $request->getCookieParams();

        // Assuming 'token' is the name of the cookie.
        if (!isset($cookies['token'])) {
            $response = new \Slim\Psr7\Response();
            return $response->withStatus(302)->withHeader('Location', '/signin');
        }

        $token = $cookies['token'] ?? '';

        try {
            $validationData = $this->signinService->validateToken($token);
            $allValidationData = json_decode($validationData, true);

            // Retrieve user role
            $userRole = $this->signinService->getUserRole($allValidationData['userId']);

            // Add userId and userRole to request attributes
            $request = $request->withAttribute('userId', $allValidationData['userId']);
            $request = $request->withAttribute('userRole', $userRole);

            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new \Slim\Psr7\Response();
            return $response->withStatus(302)->withHeader('Location', '/signin');
        }

        return $handler->handle($request);
    }
}
