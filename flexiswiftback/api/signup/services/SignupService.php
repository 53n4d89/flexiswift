<?php

namespace App\signup\services;

use App\signup\models\SignupModel;
use App\helpers\EmailUtil;
use \DateTime;

class SignupService {
    protected $signupModel;
    protected $emailUtil;

    public function __construct(SignupModel $signupModel, EmailUtil $emailUtil) {
        $this->signupModel = $signupModel;
        $this->emailUtil = $emailUtil;
    }

    public function signup($data) {
        // Check if email is already registered
        $existingUser = $this->signupModel->findByEmail($data['email']);
        $errors = [];

        if ($existingUser) {
            $errors['apologize'] = 'We apologize, but it seems that we are unable to proceed with your signup at the moment. If you encounter any issues or have any questions, please feel free to contact our support team for assistance.';
        } else {
            // Generate a unique confirmation token
            $token = bin2hex(random_bytes(50)); // 50 bytes for security

            // Add token and confirmation status to the user data
            $data['confirmation_token'] = $token;
            $data['user_confirmation'] = 0; // Not confirmed yet
            $data['token_expiry'] = date('Y-m-d H:i:s'); // Set token expiry date
            

            // Create a new user
            $result = $this->signupModel->create($data);

            if ($result) {
                // Send a confirmation email
                $subject = 'Email Confirmation';
                $body = 'Please click the link to confirm your email: <a href="https://senad-cavkusic.sarajevoweb.com/confirmemail?token=' . $token . '">Confirm Email</a>';
                $emailSent = $this->emailUtil->sendEmail($data['email'], $subject, $body);

                if ($emailSent) {
                    // Signup successful
                    return true;
                }
            } else {
                $errors['failed'] = 'Signup failed. Please try again.';
            }
        }

        return [
            'errors' => $errors
        ];
    }

    public function confirm($token) {
        if (!$token) {
            return [
                'status' => 400,
                'message' => 'Please use confirmation link as we sent.',
            ];
        }

        $user = $this->signupModel->findByToken($token);

        if (!$user) {
            return [
                'status' => 400,
                'message' => 'Invalid token',
            ];
        }

        // Check if the token is expired
        $currentDate = new DateTime();
        $tokenCreationDate = new DateTime($user['token_expiry']);

        $interval = $tokenCreationDate->diff($currentDate);
        $hours = $interval->h + ($interval->days * 24);

        if ($hours >= 1) {
            // Token expired, remove user and return error message
            $this->signupModel->remove($user['email'], $user['id']);
            return [
                'status' => 400,
                'message' => 'Token expired, please register again, and confirm your email in one hour.',
            ];
        }

        $data = [
            'id' => $user['id'],
            'confirmation_token' => null,
            'user_confirmation' => 1,
        ];

        $updated = $this->signupModel->update($data);

        if($updated) {
            return [
                'status' => 200,
                'message' => 'Email confirmed',
            ];
        }
    }
}
