<?php

namespace App\dashboard\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController {

    public function displayDashboard($request, $response) {
        ob_start();
        include(__DIR__.'/../../../views/dashboard.php');
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function getDashboardData($request, $response) {
        $userRole = $request->getAttribute('userRole');
        $responseData = ['role' => $userRole];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
