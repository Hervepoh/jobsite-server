<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generate_jwt(array $payload, int $ttl = 3600): string
{
    $key = env('JWT_SECRET','votre_clé_secrète');
    $issuedAt = time();
    $expire = $issuedAt + $ttl;

    $token = array_merge($payload, [
        'iat' => $issuedAt,
        'exp' => $expire,
    ]);

    return JWT::encode($token, $key, 'HS256');
}


function decode_jwt($token): stdClass {
  return $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
}
 