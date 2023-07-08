<?php

namespace App\forgot_password\services;

use App\forgot_password\models\ForgotPasswordModel;
use App\helpers\EmailUtil;

class ForgotPasswordService {
    private $forgotPasswordModel;
    private $emailUtil;

    public function __construct(ForgotPasswordModel $forgotPasswordModel, EmailUtil $emailUtil) {
        $this->forgotPasswordModel = $forgotPasswordModel;
        $this->emailUtil = $emailUtil;
    }

    public function handleForgotPassword($email) {
        $user = $this->forgotPasswordModel->getUserByEmail($email);

        if (!$user) {
            return ['status' => false, 'message' => 'If the provided email is registered in our system, you will receive an email with instructions on how to reset your password. Please check your inbox for further details.'];
        }

        $data['user_id'] = $user->id;
        $data['forgot_password_token'] = bin2hex(random_bytes(50)); // create a random token
        $data['token_expiry'] = date('Y-m-d H:i:s');
        $this->forgotPasswordModel->setForgotPasswordToken($data);

        $subject = "Password Reset Request";
        $body = 'Click on the following link to reset your password: <a href="https://senad-cavkusic.sarajevoweb.com/createnewpassword?token=' . $data['forgot_password_token'] . '">Create new password</a>';

        $mailSent = $this->emailUtil->sendEmail($email, $subject, $body);

        if ($mailSent) {
            return ['status' => true, 'message' => 'If the provided email is registered in our system, you will receive an email with instructions on how to reset your password. Please check your inbox for further details.'];
        } else {
            return ['status' => false, 'message' => 'Failed to send reset email'];
        }
    }
}
