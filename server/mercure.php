<?php
declare(strict_types=1);

require_once __DIR__ . '/utils.php';

function jwt_encode(array $payload, string $secret): string
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $segments = [
        base64url_encode(json_encode($header, JSON_UNESCAPED_SLASHES)),
        base64url_encode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
    ];
    $signingInput = implode('.', $segments);
    $signature = hash_hmac('sha256', $signingInput, $secret, true);
    $segments[] = base64url_encode($signature);
    return implode('.', $segments);
}

function mercure_hub_url(): string
{
    $env = getenv('MERCURE_HUB_URL');
    if (is_string($env) && $env !== '') {
        return $env;
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . '/.well-known/mercure';
}

function publish_event(string $room_hash, string $type, array $body = [], ?string $from = null): void
{
    $topic = 'room:' . $room_hash;
    $now = time();
    $payload = [
        'v' => 0,
        'type' => $type,
        'room_hash' => $room_hash,
        'from' => $from,
        'ts' => $now,
        'body' => $body,
    ];

    $jwtKey = getenv('MERCURE_PUBLISHER_JWT_KEY') ?: '!ChangeMe!';
    $jwt = jwt_encode(['mercure' => ['publish' => [$topic]]], $jwtKey);

    $postFields = http_build_query([
        'topic' => $topic,
        'data' => json_encode($payload, JSON_UNESCAPED_SLASHES),
        'type' => $type,
    ]);

    $ch = curl_init(mercure_hub_url());
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $jwt,
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_exec($ch);
    curl_close($ch);
}
