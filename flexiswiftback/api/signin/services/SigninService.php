<?php

namespace App\signin\services;

use App\signin\models\SigninModel;
use DateTime;
use Exception;

class SigninService {
    protected $signinModel;
    protected $jwtSecretKey;

    public function __construct(SigninModel $signinModel, $jwtSecretKey) {
        $this->signinModel = $signinModel;
        $this->jwtSecretKey = $jwtSecretKey;
    }

    public function signin($data) {
        $user = $this->signinModel->findByEmail($data['email']);
        $resultsGetUIds = $this->getUniqueBrowserId();
        $resultGetUId = json_decode($resultsGetUIds, true);

        if ($user) {
            if ($user['user_confirmation'] == 0) {
                return json_encode([
                    'Your account verification is pending. Kindly click on the link we have sent you subsequent to your registration to complete the verification process.'
                ]);
            }

            if ($user['admin_confirmation'] == 0) {
                return json_encode([
                    'Your account is currently pending verification by an administrator. We kindly request your patience, as you will receive a notification regarding the verification process.',
                ]);
            }



            if (password_verify($data['password'], $user['password'])) {
                $token = $this->generateToken($user, $resultGetUId);

                // Create a new refresh token
                $refreshToken = $this->generateRefreshToken();

                // Store the refresh token in the database
                $this->signinModel->storeRefreshToken($user['user_id'], $refreshToken, $resultGetUId['userAgent']);
                //$this->signinModel->storeUserIpAddress($user['id'], $ipAddress);

                $userRole = $this->getUserRole($user['user_id']);

                // Send a JSON response with the token and refresh token
                return json_encode([
                    'token' => $token,
                    'refresh_token' => $refreshToken,
                    'role' => $userRole
                ]);
            } else {
                return json_encode([
                    'The email or password provided is invalid. Please attempt again.',
                ]);
            }
        } else {
            return json_encode([
                'The email or password provided is invalid. Please attempt again.',
            ]);
        }

    }

    // New function for generating refresh tokens
    protected function generateRefreshToken() {
        // Create a random string
        return bin2hex(random_bytes(32));
    }

    protected function generateToken($user, $uniqueId) {
        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload = $this->base64UrlEncode(json_encode([
            'iss' => 'senad_cavkusic',
            'aud' => '.sarajevoweb.com',
            'iat' => time(),
            'exp' => time() + 3600,
            'idv' => $user['user_id'],
            'ipv' => $uniqueId['uniqueId']
        ]));

        $dataToSign = "$header.$payload";
        $signature = $this->base64UrlEncode(hash_hmac('sha256', $dataToSign, $this->jwtSecretKey, true));

        return "$dataToSign.$signature";
    }

    protected function getUniqueBrowserId() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }

        // Check for IPs passing through proxies
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // We need to check if it's a list of IP addresses
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            // We'll take the last IP in the list
            $ipAddress = trim(end($ipList));
        }

        // If not, we use the sent IP address (most probably it's a direct access from the user)
        else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        // Remove the dots from the IP address
        $ipAddress = str_replace('.', '', $ipAddress);
        // Convert to base64
        $uniqueId = hash('sha256', $userAgent . $ipAddress);

        return json_encode([
            'uniqueId' => $uniqueId,
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent
        ]);
    }


    // Add a method to create a new token using a refresh token
    public function createNewTokenWithRefreshToken($refreshToken) {
        $resultsGetUIds = $this->getUniqueBrowserId();
        $resultGetUId = json_decode($resultsGetUIds, true);
        $refreshTokenData = $this->signinModel->findRefreshToken($refreshToken);

        if (!$refreshTokenData) {
            throw new Exception('Invalid refresh token');
        }

        $user = $this->signinModel->findById($refreshTokenData['user_id']);

        if (!$user) {
            throw new Exception('User not found');
        }

        return $this->generateToken($user, $resultGetUId);
    }

    public function validateToken($token) {
        $resultsGetUIds = $this->getUniqueBrowserId();
        $resultGetUId = json_decode($resultsGetUIds, true);
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            throw new Exception('Invalid token format');
        }

        $headerBase64 = $tokenParts[0];
        $payloadBase64 = $tokenParts[1];
        $signatureBase64 = $tokenParts[2];

        $header = json_decode($this->base64UrlDecode($headerBase64), true);
        $payload = json_decode($this->base64UrlDecode($payloadBase64), true);
        $signature = $this->base64UrlDecode($signatureBase64);

        // Verify the header
        if (!isset($header['alg']) || $header['alg'] !== 'HS256') {
            throw new Exception('Unexpected or missing algorithm in token header');
        }

        // Verify the signature
        $expectedSignature = hash_hmac('sha256', "$headerBase64.$payloadBase64", $this->jwtSecretKey, true);
        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception('Invalid token signature');
        }

        // Verify the issuer
        if (!isset($payload['iss']) || $payload['iss'] !== 'senad_cavkusic') {
            throw new Exception('Invalid token issuer');
        }

        // Verify the token hasn't expired
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token has expired');
        }

        // Verify the IP address
        $userId = isset($payload['idv']) ? $payload['idv'] : null;
        $storedIP = isset($payload['ipv']) ? $payload['ipv'] : null;

        if($storedIP !== $resultGetUId['uniqueId']){
            throw new Exception('Invalid Token');
        }else{
            return json_encode([
                'userId' => $userId,
                'userAgent' => $resultGetUId['userAgent']
            ]);
        }
    }

    protected function base64UrlEncode($data) {
        $encoded = base64_encode($data);
        $encoded = str_replace(['+', '/', '='], ['-', '_', ''], $encoded);

        return $encoded;
    }

    protected function base64UrlDecode($data) {
        $padded = str_pad($data, strlen($data) % 4, '=', STR_PAD_RIGHT);
        return base64_decode(strtr($padded, '-_', '+/'));
    }

    public function getUserRole($userId) {
      // Assuming you have a method in the SigninModel to retrieve the user's role based on the user ID.
      return $this->signinModel->getUserRole($userId);
    }

}
