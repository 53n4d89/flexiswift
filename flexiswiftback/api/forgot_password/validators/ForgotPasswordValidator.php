<?php

namespace App\forgot_password\validators;

class ForgotPasswordValidator
{
    public function validate($requestData)
    {
        $errors = [];

        // Validate email
        if (!isset($requestData['email']) || empty(trim($requestData['email']))) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($requestData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        // Prepare the validation result
        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'data' => $requestData
        ];
    }
}
