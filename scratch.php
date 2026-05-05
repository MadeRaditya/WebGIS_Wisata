<?php
require 'app/Libraries/SupabaseClient.php';
function env($k, $d=''){
    return $k=='SUPABASE_URL'?'https://ytmblikoqxfffbmgoxcp.supabase.co':
    ($k=='SUPABASE_ANON_KEY'?'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inl0bWJsaWtvcXhmZmZibWdveGNwIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Nzc4OTE4ODIsImV4cCI6MjA5MzQ2Nzg4Mn0.qihNGvO33MbVy3lK-4uUQSwlLR0aj9kJQxkp2xj8wjg':$d);
}
function log_message($l, $m) { echo "LOG: $m\n"; }
$c=new \App\Libraries\SupabaseClient();
print_r($c->from('admin')->select()->get());
