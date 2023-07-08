<?php

namespace App\services\controllers;

class ServicesController {
    public function displayServices($request, $response) {
        // Assume that the output buffer has been started in your index.php or elsewhere
        ob_start();
        include(__DIR__.'/../../../views/services.php');
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    }
}
