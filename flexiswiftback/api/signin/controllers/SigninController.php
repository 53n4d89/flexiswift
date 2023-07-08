<?php

namespace App\signin\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\signin\validators\SigninValidator;
use App\signin\services\SigninService;
use Exception;

class SigninController {
    protected $signinValidator;
    protected $signinService;

    public function __construct(SigninValidator $signinValidator, SigninService $signinService) {
        $this->signinValidator = $signinValidator;
        $this->signinService = $signinService;
    }

    public function displaySigninForm(Request $request, Response $response, $errors = []) {
        // Retrieve the cookie
        $cookies = $request->getCookieParams();

        // Check if the 'token' cookie exists
        if (isset($cookies['token'])) {
            // Validate the token
            try {
                $this->signinService->validateToken($cookies['token']);
                // If the token is valid, redirect the user
                return $response->withHeader('Location', '/dashboard')->withStatus(302);

              } catch (Exception $e) {
                // do nothing
              }
        }

        ob_start();
        include __DIR__ . '/../../../views/signin_form.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function authenticateUser(Request $request, Response $response) {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $data = json_decode($request->getBody()->getContents(), TRUE);
        } else {
            $data = $request->getParsedBody();
        }

        $validation = $this->signinValidator->validate($data);

        if (!$validation['isValid']) {
            $response->getBody()->write(json_encode($validation['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $result = $this->signinService->signin($data);
        $result = json_decode($result, true);  // Add this line to decode JSON into an associative array

        if (is_array($result) && array_key_exists('token', $result)) {
            $response = $response->withHeader('Set-Cookie', 'token=' . $result['token'] . '; HttpOnly; Secure; SameSite=Strict; Path=/');
            $response->getBody()->write(json_encode(['token' => $result['token'], 'refresh_token' => $result['refresh_token']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => $result]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function refreshToken(Request $request, Response $response) {
        $contentType = $request->getHeaderLine('Content-Type');
        $data = json_decode($request->getBody()->getContents(), TRUE);
        $refreshToken = $data['refreshToken'] ?? null;
        if(!$refreshToken) {
            $response->getBody()->write(json_encode(['message' => 'Refresh token is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $newToken = $this->signinService->createNewTokenWithRefreshToken($refreshToken);
            $response->getBody()->write(json_encode(['token' => $newToken]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}
