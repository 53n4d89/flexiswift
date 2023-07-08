<?php

namespace App\signup\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\signup\validators\SignupValidator;
use App\signup\services\SignupService;

class SignupController {
    protected $signupValidator;
    protected $signupService;

    public function __construct(SignupValidator $signupValidator, SignupService $signupService) {
        $this->signupValidator = $signupValidator;
        $this->signupService = $signupService;
    }

    public function displaySignupFormOptions(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/signup_form_options.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function displayOrganizationSignupForm(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/organization_signup_form.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function displayUserSignupForm(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/user_signup_form.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function displayUserSignupSuccess(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/user_signup_success.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function displayOrganizationSignupSuccess(Request $request, Response $response, $errors = []) {
        ob_start();
        include __DIR__ . '/../../../views/organization_signup_success.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function signupUser(Request $request, Response $response) {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $data = json_decode($request->getBody()->getContents(), TRUE);
        } else {
            $data = $request->getParsedBody();
        }

        $validation = $this->signupValidator->validateUser($data);

        if (!$validation['isValid']) {
            $response->getBody()->write(json_encode($validation['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $data['role'] = 'User';
        $result = $this->signupService->signup($data);

        if ($result === true) {
            $response->getBody()->write(json_encode(['message' => 'Successful signup!']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode($result['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function signupOrganization(Request $request, Response $response) {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $data = json_decode($request->getBody()->getContents(), TRUE);
        } else {
            $data = $request->getParsedBody();
        }

        $validation = $this->signupValidator->validateOrganization($data);

        if (!$validation['isValid']) {
            $response->getBody()->write(json_encode($validation['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $data['role'] = 'Admin';
        $result = $this->signupService->signup($data);

        if ($result === true) {
            $response->getBody()->write(json_encode(['message' => 'Successful signup!']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode($result['errors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function confirmEmail(Request $request, Response $response) {
        $token = $request->getQueryParams()['token'];
        $result = $this->signupService->confirm($token);

        $response->getBody()->write(json_encode(['message' => $result['message']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
    }

}
