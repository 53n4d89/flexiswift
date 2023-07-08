<?php

namespace App\signin\models;

use PDO;
use PDOException;

class SigninModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Find user by email
    public function findByEmail($email){
        $sql = "SELECT users.*, signup_confirmation_token.*
                FROM users
                INNER JOIN signup_confirmation_token ON users.id = signup_confirmation_token.user_id
                WHERE users.email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find user by id
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Store refresh token
    public function storeRefreshToken($userId, $refreshToken, $userAgent) {
        // If user exists, store the refresh token
        $sql = "INSERT INTO refresh_tokens (user_id, userAgent, token, created_at) VALUES (:userId, :userAgent, :token, :created_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'userAgent' => $userAgent,
            'token' => $refreshToken,
            'created_at' => date('Y-m-d H:i:s') // Current date and time
        ]);
    }

    //public function storeUserIpAddress($userId, $ipAddress) {
      //  $stmt = $this->pdo->prepare('INSERT INTO userIpAdresses (user_id, ip_address) VALUES (:user_id, :ip_address)');
        //$stmt->execute([
          //  'user_id' => $userId,
            //'ip_address' => $ipAddress,
      //  ]);
    //}

    // Find refresh token
    public function findRefreshToken($refreshToken) {
        $sql = "SELECT * FROM refresh_tokens WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $refreshToken]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserRole($userId) {
        $query = 'SELECT role FROM roles WHERE user_id = :userId';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['role'] ?? null;
    }
}
