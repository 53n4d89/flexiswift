<?php

namespace App\create_new_password\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\create_new_password\validators\CreateNewPasswordValidator;
use App\create_new_password\services\CreateNewPasswordService;

class CreateNewPasswordController {
    protected $createNewPasswordValidator;
    protected $createNewPasswordService;

    public function __construct(CreateNewPasswordValidator $createNewPasswordValidator, CreateNewPasswordService $createNewPasswordService) {
        $this->createNewPasswordValidator = $createNewPasswordValidator;
        $this->createNewPasswordService = $createNewPasswordService;
    }

    public function displayCreateNewPasswordForm(Request $request, Response $response, $errors = []) {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['token'])) {
            // Token not provided, return error message
            $response->getBody()->write(json_encode([
                'message' => 'Token is required',
                'status' => 400
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $token = $queryParams['token'];
        $tokenValidationResult = $this->createNewPasswordService->isValidToken($token);

        if ($tokenValidationResult['success'] == false) {
            // handle invalid or expired token case, maybe redirect to an error page
            $response->getBody()->write(json_encode([
                'message' => $tokenValidationResult['message'],
                'status' => $tokenValidationResult['status']
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        ob_start();
        include __DIR__ . '/../../../views/create_new_password_form.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function displayNewPasswordSuccess(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/new_password_success.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function handleCreateNewPasswordRequest(Request $request, Response $response) {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $data = json_decode($request->getBody()->getContents(), TRUE);
        } else {
            $data = $request->getParsedBody();
        }

        $validation = $this->createNewPasswordValidator->validate($data);

        if (!$validation['isValid']) {
            $response->getBody()->write(json_encode($validation['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        error_log($data['token']);
        $result = $this->createNewPasswordService->handleCreateNewPassword($data, $data['token']);

        if ($result['success']) {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
        }
    }
}
