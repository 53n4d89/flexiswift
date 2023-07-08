<?php

namespace App\signup\validators;

use App\helpers\SanitizationHelper;

class SignupValidator {
    public function validateUser($data) {
        $errors = [];

        if (!SanitizationHelper::isValidUsername($data['username'])) {
            $errors['username'] = 'Username is required and must be at least 3 characters.';
        } else {
            $data['username'] = SanitizationHelper::sanitizeString($data['username']);
        }

        if (!SanitizationHelper::isValidEmail($data['email'])) {
            $errors['email'] = 'Email is required and must be a valid email address.';
        } else {
            $data['email'] = SanitizationHelper::sanitizeEmail($data['email']);
        }

        if (!SanitizationHelper::isValidPassword($data['password'])) {
            $errors['password'] = 'Password is required and must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one symbol.';
        }

        if (!SanitizationHelper::passwordsMatch($data['password'], $data['repeat_password'])) {
            $errors['repeat_password'] = 'Password and Repeat Password must match.';
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }

    public function validateOrganization($data) {
        $validationResult = $this->validateUser($data);

        if (!SanitizationHelper::isValidOrgname($data['orgname'])) {
            $validationResult['errors']['orgname'] = 'Organization name is required and must be at least 3 characters.';
            $validationResult['isValid'] = false;
        } else {
            $validationResult['data']['orgname'] = SanitizationHelper::sanitizeString($data['orgname']);
        }

        return $validationResult;
    }
}
