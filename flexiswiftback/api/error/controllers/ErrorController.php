<?php

namespace App\error\controllers;

class ErrorController {
    public function display404($request, $response) {
        // Assume that the output buffer has been started in your index.php or elsewhere
        ob_start();
        include(__DIR__.'/../../../views/404.php');
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    }
}
