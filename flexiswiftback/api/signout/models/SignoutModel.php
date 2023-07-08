<?php

namespace App\signout\models;

use PDO;
use PDOException;

class SignoutModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function deleteByUserId($userId, $userAgent) {
        $sql = "DELETE FROM refresh_tokens WHERE user_id = :userId AND userAgent = :userAgent";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'userAgent' => $userAgent// Current date and time
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
