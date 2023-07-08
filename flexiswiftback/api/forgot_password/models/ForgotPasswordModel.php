<?php

namespace App\forgot_password\models;

use PDO;
use PDOException;

class ForgotPasswordModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // Log this error in real application
            return false;
        }
    }

    public function setForgotPasswordToken($data) {
        $sql = "INSERT INTO forgot_password_token (user_id, forgot_password_token, token_expiry) VALUES (:user_id, :forgot_password_token, :token_expiry)";
        $stmt = $this->db->prepare($sql);

        $parameters = [
            'user_id' => $data['user_id'],
            'forgot_password_token' => $data['forgot_password_token'],
            'token_expiry' => $data['token_expiry'],
        ];

        try {
            $stmt->execute($parameters);
            $userId = $this->db->lastInsertId(); // Get the ID of the newly created user
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->db->rollBack(); // If an error occurred, roll back the transaction
            return false;
        }
    }
}
