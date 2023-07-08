<?php

namespace App\helpers;

class SanitizationHelper {
    public static function sanitizeString($string) {
        return htmlspecialchars(strip_tags($string), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public static function isValidUsername($username) {
        return !empty($username) && strlen($username) >= 3;
    }

    public static function isValidOrgname($orgname) {
        return !empty($orgname) && strlen($orgname) >= 3;
    }

    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isValidPassword($password) {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        return strlen($password) >= 8 && $uppercase && $lowercase && $number && $specialChars;
    }

    public static function passwordsMatch($password, $repeatPassword) {
        return $password === $repeatPassword;
    }
}
