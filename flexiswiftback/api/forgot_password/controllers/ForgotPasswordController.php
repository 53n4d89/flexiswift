<?php

namespace App\forgot_password\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\forgot_password\validators\ForgotPasswordValidator;
use App\forgot_password\services\ForgotPasswordService;

class ForgotPasswordController {
    protected $forgotPasswordValidator;
    protected $forgotPasswordService;

    public function __construct(ForgotPasswordValidator $forgotPasswordValidator, ForgotPasswordService $forgotPasswordService) {
        $this->forgotPasswordValidator = $forgotPasswordValidator;
        $this->forgotPasswordService = $forgotPasswordService;
    }

    public function displayForgotPasswordForm(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/forgot_password_form.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function handleForgotPasswordRequest(Request $request, Response $response) {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $data = json_decode($request->getBody()->getContents(), true);
        } else {
            $data = $request->getParsedBody();
        }

        $validationResult = $this->forgotPasswordValidator->validate($data);

        if (!$validationResult['isValid']) {
            $response->getBody()->write(json_encode($validationResult['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $email = $data['email'];
        $forgotPasswordResult = $this->forgotPasswordService->handleForgotPassword($email);

        if ($forgotPasswordResult['status']) {
            // Show success message
            $response->getBody()->write(json_encode(['message' => $forgotPasswordResult['message']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            // Show error message
            $response->getBody()->write(json_encode(['message' => $forgotPasswordResult['message']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
