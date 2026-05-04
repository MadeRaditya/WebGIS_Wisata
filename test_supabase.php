<?php

// Mock the environment for CI4
define('FCPATH', __DIR__ . '/public/');
require __DIR__ . '/system/Test/bootstrap.php';

use App\Libraries\SupabaseClient;

// Manually load env since we are in a script
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . "=" . trim($value));
    }
}

$client = new SupabaseClient();
echo "Testing connection to Supabase...\n";
echo "URL: " . getenv('SUPABASE_URL') . "\n";

try {
    $results = $client->from('kategori')->select('*')->get();
    echo "Results from 'kategori':\n";
    print_r($results);
    
    if (empty($results)) {
        echo "No data found. Did you run the SQL in database_setup.sql?\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
