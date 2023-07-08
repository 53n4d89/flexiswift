<?php

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use App\signup\models\SignupModel;
use App\signup\validators\SignupValidator;
use App\signin\models\SigninModel;
use App\signin\validators\SigninValidator;
use App\signin\services\SigninService;
use App\forgot_password\models\ForgotPasswordModel;
use App\forgot_password\validators\ForgotPasswordValidator;
use App\forgot_password\services\ForgotPasswordService;
use App\helpers\EmailUtil;
use App\middleware\SignupSuccessMiddleware;
use App\middleware\AuthMiddleware;


// Include the settings file
$settings = require __DIR__ . '/settings.php';

return function (ContainerInterface $container) use ($settings) {
    $container->set(ServerRequestFactory::class, function ($container) {
        return new ServerRequestFactory();
    });

    $container->set(ResponseFactory::class, function ($container) {
        return new ResponseFactory();
    });

    $container->set(StreamFactory::class, function ($container) {
        return new StreamFactory();
    });

    $container->set(UploadedFileFactory::class, function ($container) {
        return new UploadedFileFactory();
    });

    // Add database connection to the container
    $container->set(PDO::class, function ($container) use ($settings) {
        $dbSettings = $settings['db'];
        $host = $dbSettings['host'];
        $dbname = $dbSettings['dbname'];
        $user = $dbSettings['user'];
        $pass = $dbSettings['pass'];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $pass, $options);
    });

    // Register the SignupModel dependency
    $container->set(SignupModel::class, function ($container) {
        $db = $container->get(PDO::class);
        return new SignupModel($db);
    });

    // Register the SignupValidator dependency
    $container->set(SignupValidator::class, function ($container) {
        // Add any necessary dependencies for the SignupValidator class
        return new SignupValidator();
    });

    // Register the SigninModel dependency
    $container->set(SigninModel::class, function ($container) {
        $db = $container->get(PDO::class);
        return new SigninModel($db);
    });

    // Register the SigninValidator dependency
    $container->set(SigninValidator::class, function ($container) {
        // Add any necessary dependencies for the SigninValidator class
        return new SigninValidator();
    });

    $container->set('AuthMiddleware', function ($container) {
        $signinService = $container->get(SigninService::class);
        return new \App\middleware\AuthMiddleware($signinService);
    });

    // Register the SigninService dependency
    $container->set(SigninService::class, function ($container) use ($settings) {
        $signinModel = $container->get(SigninModel::class);
        $jwtSecretKey = $settings['jwt']['secret_key'];
        return new SigninService($signinModel, $jwtSecretKey);
    });

    // Register the ForgotPasswordModel dependency
    $container->set(ForgotPasswordModel::class, function ($container) {
        $db = $container->get(PDO::class);
        return new ForgotPasswordModel($db);
    });

    // Register the EmailUtil dependency
    $container->set(EmailUtil::class, function ($container) {
        // Add any necessary dependencies for the EmailUtil class
        return new EmailUtil();
    });

    // Register the ForgotPasswordValidator dependency
    $container->set(ForgotPasswordValidator::class, function ($container) {
        // Add any necessary dependencies for the ForgotPasswordValidator class
        return new ForgotPasswordValidator();
    });

    // Register the ForgotPasswordService dependency
    $container->set(ForgotPasswordService::class, function ($container) {
        $forgotPasswordModel = $container->get(ForgotPasswordModel::class);
        $emailUtil = $container->get(EmailUtil::class);
        return new ForgotPasswordService($forgotPasswordModel, $emailUtil);
    });

    // Register the ForgotPasswordController dependency
    $container->set(\App\forgot_password\controllers\ForgotPasswordController::class, function ($container) {
        $forgotPasswordValidator = $container->get(ForgotPasswordValidator::class);
        $forgotPasswordService = $container->get(ForgotPasswordService::class);
        $forgotPasswordModel = $container->get(ForgotPasswordModel::class);
        // Add any other dependencies for the ForgotPasswordController class
        return new \App\forgot_password\controllers\ForgotPasswordController($forgotPasswordValidator, $forgotPasswordService, $forgotPasswordModel);
    });

    $container->set('signupSuccessMiddleware', function () {
        return new SignupSuccessMiddleware();
    });

    $container->set('settings', $settings);
};
