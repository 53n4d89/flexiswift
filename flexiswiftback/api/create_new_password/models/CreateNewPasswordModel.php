<?php

namespace App\create_new_password\models;

use PDO;
use PDOException;

class CreateNewPasswordModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByToken($token) {
        try {
            $sql = "SELECT users.*, forgot_password_token.token_expiry
                    FROM users INNER JOIN forgot_password_token ON users.id = forgot_password_token.user_id
                    WHERE forgot_password_token.forgot_password_token = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['token' => $token]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Remove token
    public function removeByToken($token) {
        // Delete from signup_confirmation_token table
        $sql = "DELETE FROM forgot_password_token WHERE forgot_password_token = :token";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute(['token' => $token]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
        return true;
    }

    public function updatePassword($data, $token) {
        $hashedPassword = $this->hashPassword($data['password']);
        // First, find the user_id by the token
        $user = $this->findByToken($token);

        if (!$user) {
            // User not found, handle this case as needed
            return false;
        }

        try {
            $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
            return $stmt->execute([
                'password' => $hashedPassword,
                'id' => $user['id']
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Hash password
    private function hashPassword($password) {
        // Using PASSWORD_ARGON2I as it is currently recommended by PHP
        return password_hash($password, PASSWORD_ARGON2I);
    }
}
