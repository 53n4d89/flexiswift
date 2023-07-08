<?php

namespace App\blog\controllers;

class BlogController {
    public function displayBlog($request, $response) {
        // Assume that the output buffer has been started in your index.php or elsewhere
        ob_start();
        include(__DIR__.'/../../../views/blog.php');
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    }
}
