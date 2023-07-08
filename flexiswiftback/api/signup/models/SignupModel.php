<?php

namespace App\signup\models;

use PDO;
use PDOException;

class SignupModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Add user
    public function create($data) {
        // start transaction
        $this->db->beginTransaction();

        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->db->prepare($sql);

        $parameters = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $this->hashPassword($data['password']),
        ];

        try {
            $stmt->execute($parameters);
            $userId = $this->db->lastInsertId(); // Get the ID of the newly created user
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->db->rollBack(); // If an error occurred, roll back the transaction
            return false;
        }

        $sql = "INSERT INTO signup_confirmation_token (user_id, confirmation_token, user_confirmation, token_expiry) VALUES (:user_id, :confirmation_token, :user_confirmation, :token_expiry)";
        $stmt = $this->db->prepare($sql);

        $parameters = [
            'user_id' => $userId,
            'confirmation_token' => $data['confirmation_token'],
            'user_confirmation' => $data['user_confirmation'],
            'token_expiry' => $data['token_expiry']
        ];

        try {
            $stmt->execute($parameters);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->db->rollBack(); // If an error occurred, roll back the transaction
            return false;
        }

        $sql = "INSERT INTO roles (user_id, role) VALUES (:user_id, :role)";
        $stmt = $this->db->prepare($sql);

        $parameters = [
            'user_id' => $userId,
            'role' => $data['role']
        ];

        try {
            $stmt->execute($parameters);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->db->rollBack(); // If an error occurred, roll back the transaction
            return false;
        }

        // Only insert into organization if orgname is provided
        if (isset($data['orgname']) && !empty($data['orgname'])) {
            $sql = "INSERT INTO organization (user_id, organization_name) VALUES (:user_id, :orgname)";
            $stmt = $this->db->prepare($sql);

            $parameters = [
                'user_id' => $userId,
                'orgname' => $data['orgname']
            ];

            try {
                $stmt->execute($parameters);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $this->db->rollBack(); // If an error occurred, roll back the transaction
                return false;
            }
        }

        // commit transaction if all inserts were successful
        $this->db->commit();
        return true;
    }

    // Hash password
    private function hashPassword($password) {
        // Using PASSWORD_ARGON2I as it is currently recommended by PHP
        return password_hash($password, PASSWORD_ARGON2I);
    }

    // Find user by email
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find user by token
    public function findByToken($token) {
        $sql = "SELECT users.*, signup_confirmation_token.token_expiry
                FROM users
                INNER JOIN signup_confirmation_token ON users.id = signup_confirmation_token.user_id
                WHERE signup_confirmation_token.confirmation_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Remove user
    public function remove($email, $userId) {
        // Start the transaction
        $this->db->beginTransaction();

        // Delete from signup_confirmation_token table
        $sql = "DELETE FROM signup_confirmation_token WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->db->rollBack();
            return false;
        }

        // Delete from users table
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute(['email' => $email]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->db->rollBack();
            return false;
        }

        // If no exceptions were thrown, then both deletions were successful
        $this->db->commit();
        return true;
    }

    public function update($data) {
        $sql = "UPDATE signup_confirmation_token
                SET confirmation_token = :confirmation_token, user_confirmation = :user_confirmation
                WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);

        $parameters = [
            ':user_id' => $data['id'],
            ':confirmation_token' => $data['confirmation_token'],
            ':user_confirmation' => $data['user_confirmation']
        ];

        try {
            $stmt->execute($parameters);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
