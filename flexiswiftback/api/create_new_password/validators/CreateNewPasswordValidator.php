<?php

namespace App\create_new_password\validators;

use App\helpers\SanitizationHelper;

class CreateNewPasswordValidator {
    public function validate($data) {
        $errors = [];

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
}
