<?php

require_once __DIR__ . '/db.php';

const ACCOUNT = '';
const CLIENT_ID = '';
const CLIENT_SECRET = '';
const REDIRECT_URI = '';

const BASE_URL = 'https://' . ACCOUNT . '.amocrm.ru';
const API_BASE_URL = BASE_URL . '/api/v4';

function amo_getLastLeads()
{
    return amo_apiGet('/leads?' . http_build_query([
        'limit' => 10,
        'order[created_at]' => 'desc',
    ]));
}

function amo_apiGet($url)
{
    $token = amo_getAndUpdateToken();

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => API_BASE_URL . $url,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $token",
        ],
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $response = curl_exec($ch);

    return json_decode($response, true);
}

function amo_getAndUpdateToken()
{
    global $db;

    $dbToken = $db->query('SELECT * FROM amo_token')->fetch();

    if (strtotime($dbToken['expires_at']) < time()) {
        $newToken = amo_getTokenByRefreshToken($dbToken['refresh_token']);

        amo_updateToken($newToken);

        return $newToken['access_token'];
    }

    return $dbToken['access_token'];
}

function amo_updateToken($token)
{
    global $db;

    $db->prepare(
        'UPDATE amo_token SET
            access_token = ?,
            refresh_token = ?,
            expires_at = ?'
    )->execute([
        $token['access_token'],
        $token['refresh_token'],
        date('Y-m-d H:i:s', strtotime("+$token[expires_in] sec"))
    ]);
}

function amo_getTokenByRefreshToken($refreshToken)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => BASE_URL . '/oauth2/access_token',
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'redirect_uri' => REDIRECT_URI,
        ]),
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $response = curl_exec($ch);

    return json_decode($response, true);
}

function amo_getTokenByCode($code)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => BASE_URL . '/oauth2/access_token',
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => REDIRECT_URI,
        ]),
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $response = curl_exec($ch);

    return json_decode($response, true);
}
