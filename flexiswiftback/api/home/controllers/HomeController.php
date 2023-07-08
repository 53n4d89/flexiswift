<?php

namespace App\home\controllers;

class HomeController {
    public function displayHome($request, $response) {
        // Assume that the output buffer has been started in your index.php or elsewhere
        ob_start();
        include(__DIR__.'/../../../views/home.php');
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    }
}
