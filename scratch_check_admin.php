<?php
$envPath = __DIR__ . '/.env';
$url = '';
$key = '';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) == 2) {
            $k = trim($parts[0]);
            $v = trim($parts[1], " \t\n\r\0\x0B\"'");
            if ($k === 'SUPABASE_URL') $url = $v;
            if ($k === 'SERVICE_ROLE_KEY') $key = $v;
        }
    }
}

function checkTable($tableName, $url, $key) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/rest/v1/' . $tableName . '?select=*&limit=1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $key,
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $httpCode, 'response' => $response];
}

$adminCheck = checkTable('admin', $url, $key);
echo "Admin table check: " . $adminCheck['code'] . " - " . $adminCheck['response'] . "\n";

$usersCheck = checkTable('users', $url, $key);
echo "Users table check: " . $usersCheck['code'] . " - " . $usersCheck['response'] . "\n";
