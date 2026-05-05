<?php
$envPath = __DIR__ . '/.env';
$url = '';
$key = ''; // Anon key for auth
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) == 2) {
            $k = trim($parts[0]);
            $v = trim($parts[1], " \t\n\r\0\x0B\"'");
            if ($k === 'SUPABASE_URL') $url = $v;
            if ($k === 'SUPABASE_ANON_KEY') $key = $v;
        }
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '/auth/v1/token?grant_type=password');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'admin@mail.com', 'password' => 'admin1234']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Auth login check: $httpCode - $response\n";
