<?php

namespace App\signout\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\signin\services\SigninService;
use App\signout\services\SignoutService;



class SignoutController {
    protected $signoutService;
    protected $signinService;

    public function __construct(SignoutService $signoutService, SigninService $signinService) {
        $this->signoutService = $signoutService;
        $this->signinService = $signinService;
    }

    public function signout(Request $request, Response $response) {
        $cookies = $request->getCookieParams();
        $token = $cookies['token'] ?? '';
        $validationData = $this->signinService->validateToken($token);
        $allValidationData = json_decode($validationData, true);

        $this->signoutService->deleteRefreshToken($allValidationData['userId'], $allValidationData['userAgent']);
        // Clear the 'token' cookie
        setcookie('token', '', time() - 3600, '/', '', true, true);
        // Redirect to the login page
        $response->getBody()->write(json_encode(['message' => 'Successful signout!']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
