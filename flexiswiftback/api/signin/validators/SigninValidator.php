<?php

namespace App\signin\validators;

use App\helpers\SanitizationHelper;

class SigninValidator {
    public function validate($data) {
        $errors = [];

        if (!SanitizationHelper::isValidEmail($data['email'])) {
            $errors['email'] = 'Email is required and must be a valid email address.';
        } else {
            $data['email'] = SanitizationHelper::sanitizeEmail($data['email']);
        }

        if (!isset($data['password']) || empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }
}
