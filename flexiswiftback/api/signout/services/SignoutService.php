<?php

namespace App\signout\services;

use App\signout\models\SignoutModel;

class SignoutService {
    protected $model;

    public function __construct(SignoutModel $model) {
        $this->model = $model;
    }

    public function deleteRefreshToken($userId, $uniqueIp) {
        $this->model->deleteByUserId($userId, $uniqueIp);
    }
}
