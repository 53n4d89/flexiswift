<?php

namespace App\create_new_password\services;

use App\create_new_password\models\CreateNewPasswordModel;
use App\helpers\EmailUtil;
use \DateTime;

class CreateNewPasswordService {
    private $createNewPasswordModel;
    private $emailUtil;

    public function __construct(CreateNewPasswordModel $createNewPasswordModel, EmailUtil $emailUtil) {
        $this->createNewPasswordModel = $createNewPasswordModel;
        $this->emailUtil = $emailUtil;
    }

    public function handleCreateNewPassword($data, $token) {
        $updated = $this->createNewPasswordModel->updatePassword($data, $token);

        if($updated) {
            $this->createNewPasswordModel->removeByToken($token);
            return [
                'success' => true,
                'status' => 200,
                'message' => 'Password changed',
            ];
        } else {
            return [
                'success' => false,
                'status' => 400,
                'message' => 'Update failed',
            ];
        }
    }

    public function isValidToken($token) {
        if (!$token) {
            return [
                'success' => false,
                'status' => 400,
                'message' => 'Please use confirmation link as we sent.',
            ];
        }

        $user = $this->createNewPasswordModel->findByToken($token);

        if (!$user) {
            return [
                'success' => false,
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
            $this->createNewPasswordModel->removeByToken($token);
            return [
                'success' => false,
                'status' => 400,
                'message' => 'The link for resetting your password has expired for security reasons. Please initiate the password recovery process again. Remember, for your protection, the new link will remain active for one hour after being sent. Thank you for your understanding and cooperation.',
            ];
        }

        // Token is valid and not expired, return success
         return [
             'success' => true
         ];
    }

}
