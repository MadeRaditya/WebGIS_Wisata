<?php
$url = 'https://ytmblikoqxfffbmgoxcp.supabase.co';
$key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inl0bWJsaWtvcXhmZmZibWdveGNwIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Nzc4OTE4ODIsImV4cCI6MjA5MzQ2Nzg4Mn0.qihNGvO33MbVy3lK-4uUQSwlLR0aj9kJQxkp2xj8wjg';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '/rest/v1/kategori');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['nama' => 'Test', 'slug' => 'test']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $key,
    'Authorization: Bearer ' . $key,
    'Content-Type: application/json',
    'Prefer: return=representation'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
