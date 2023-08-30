<?php

date_default_timezone_set('Europe/Moscow');

$db = new PDO(
    'mysql:host=;dbname=',
    '',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
