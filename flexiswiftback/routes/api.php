<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


return function (App $app) {
    // Signup routes
    $app->get('/signup', '\App\signup\controllers\SignupController:displaySignupFormOptions');
    $app->get('/signup/organization', '\App\signup\controllers\SignupController:displayOrganizationSignupForm');
    $app->get('/signup/user', '\App\signup\controllers\SignupController:displayUserSignupForm');
    $app->get('/usersignupsuccess', '\App\signup\controllers\SignupController:displayUserSignupSuccess')->add($app->getContainer()->get('signupSuccessMiddleware'));
    $app->get('/organizationsignupsuccess', '\App\signup\controllers\SignupController:displayOrganizationSignupSuccess')->add($app->getContainer()->get('signupSuccessMiddleware'));

    $app->post('/api/signup/organization/', '\App\signup\controllers\SignupController:signupOrganization');
    $app->post('/api/signup/user/', '\App\signup\controllers\SignupController:signupUser');

    // Email confirmation route
    $app->get('/confirmemail', '\App\signup\controllers\SignupController:confirmEmail'); //UI not done

    // Signin routes
    $app->get('/signin', '\App\signin\controllers\SigninController:displaySigninForm');
    $app->post('/api/signin/', '\App\signin\controllers\SigninController:authenticateUser');
    $app->post('/api/refreshToken/', '\App\signin\controllers\SigninController:refreshToken');

    $app->post('/api/signout/', 'App\signout\controllers\SignoutController:signout');

    // Forgot password routes
    $app->get('/forgotpassword', '\App\forgot_password\controllers\ForgotPasswordController:displayForgotPasswordForm');
    $app->post('/api/forgotpassword/', '\App\forgot_password\controllers\ForgotPasswordController:handleForgotPasswordRequest');

    $app->get('/createnewpassword', '\App\create_new_password\controllers\CreateNewPasswordController:displayCreateNewPasswordForm');
    $app->get('/newpasswordsuccess', '\App\create_new_password\controllers\CreateNewPasswordController:displayNewPasswordSuccess');
    $app->post('/api/createnewpassword/', '\App\create_new_password\controllers\CreateNewPasswordController:handleCreateNewPasswordRequest');

    // Password reset routes
    $app->get('/resetpassword', '\App\password_reset\controllers\PasswordResetController:displayResetForm')->add($app->getContainer()->get('AuthMiddleware')); //not created jet
    $app->post('/api/resetpassword/', '\App\password_reset\controllers\PasswordResetController:resetPassword')->add($app->getContainer()->get('AuthMiddleware')); //not created jet

    // Dashboard route
    $app->get('/dashboard', '\App\dashboard\controllers\DashboardController:displayDashboard')->add($app->getContainer()->get('AuthMiddleware'));
    $app->get('/dashboard/data', '\App\dashboard\controllers\DashboardController:getDashboardData')->add($app->getContainer()->get('AuthMiddleware'));

    // profile route
    $app->get('/profile', '\App\profile\controllers\ProfileController:displayProfileDashboard')->add($app->getContainer()->get('AuthMiddleware'));//not created jet

    // Home, error and other routes
    $app->get('/', '\App\home\controllers\HomeController:displayHome');
    $app->get('/certificates', '\App\certificates\controllers\CertificatesController:displayCertificates');
    $app->get('/blog', '\App\blog\controllers\BlogController:displayBlog');
    $app->get('/services', '\App\services\controllers\ServicesController:displayServices');

    // Error route
    $app->get('/404', '\App\error\controllers\ErrorController:display404');

    // Handle OPTIONS request for CORS preflight //set this route by your own -> currently this way is not secure
    $app->options('/api/signin/', function (Request $request, Response $response) {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'POST');
    });

    // Fallback route
    $app->any('/{routes:.+}', function (Request $request, Response $response): Response {
        return $response
           ->withStatus(302)
           ->withHeader('Location', '/404');
    });
};
