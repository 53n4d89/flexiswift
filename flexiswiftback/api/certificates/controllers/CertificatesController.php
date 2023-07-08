<?php

namespace App\certificates\controllers;

class CertificatesController {
    public function displayCertificates($request, $response) {
        // Assume that the output buffer has been started in your index.php or elsewhere
        ob_start();
        include(__DIR__.'/../../../views/certificates.php');
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    }
}
