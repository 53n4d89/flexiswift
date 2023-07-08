<?php

namespace App\superadmin\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SuperAdminController {
    public function displaySuperAdminDashboard($request, $response) {
        // Assume that the output buffer has been started in your index.php or elsewhere
        ob_start();
        include(__DIR__.'/../../../views/super_admin_panel.php');
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    }

    public function signout(Request $request, Response $response) {
        // Clear the 'token' cookie
        setcookie('token', '', time() - 3600, '/', '', true, true);

        // Redirect to the login page
        return $response->withHeader('Location', '/signin')->withStatus(302);
    }
}
