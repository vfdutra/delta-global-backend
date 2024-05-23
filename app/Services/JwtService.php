<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    protected $secret = 'ocf2fzlC17jpFeTovk72dhtSOc54USsXEar5QZ4LmEsOCzjHNN1XuVkkJfN/2aYpG10EMcrZzrv0ilaRSma1Hg==';

    public function generateToken($payload)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
