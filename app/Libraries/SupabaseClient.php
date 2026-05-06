<?php

namespace App\Libraries;

class SupabaseClient
{
    private string $url;
    private string $apiKey;

    public function __construct()
    {
        $url = env('SUPABASE_URL');
        if (empty($url)) $url = getenv('SUPABASE_URL');
        if (empty($url)) $url = $_ENV['SUPABASE_URL'] ?? 'https://ytmblikoqxfffbmgoxcp.supabase.co';
        
        // Coba gunakan SERVICE ROLE KEY terlebih dahulu (untuk bypass RLS saat CRUD)
        $key = env('SUPABASE_SERVICE_ROLE_KEY');
        if (empty($key)) $key = getenv('SUPABASE_SERVICE_ROLE_KEY');
        if (empty($key)) $key = $_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? '';
        
        if (empty($key)) $key = env('SERVICE_ROLE_KEY');
        if (empty($key)) $key = getenv('SERVICE_ROLE_KEY');
        if (empty($key)) $key = $_ENV['SERVICE_ROLE_KEY'] ?? '';

        // Fallback: Parse .env file manually if keys are still empty
        if (empty($url) || empty($key)) {
            $envPath = FCPATH . '../.env';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos(trim($line), '#') === 0) continue;
                    $parts = explode('=', $line, 2);
                    if (count($parts) == 2) {
                        $k = trim($parts[0]);
                        $v = trim($parts[1], " \t\n\r\0\x0B\"'");
                        if ($k === 'SUPABASE_URL' && empty($url)) $url = $v;
                        if (($k === 'SUPABASE_SERVICE_ROLE_KEY' || $k === 'SERVICE_ROLE_KEY') && empty($key)) $key = $v;
                    }
                }
            }
        }

        // Jika tidak ada SERVICE ROLE KEY, fallback ke ANON KEY
        if (empty($key)) {
            $key = env('SUPABASE_ANON_KEY');
            if (empty($key)) $key = getenv('SUPABASE_ANON_KEY');
            if (empty($key)) $key = $_ENV['SUPABASE_ANON_KEY'] ?? 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inl0bWJsaWtvcXhmZmZibWdveGNwIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Nzc4OTE4ODIsImV4cCI6MjA5MzQ2Nzg4Mn0.qihNGvO33MbVy3lK-4uUQSwlLR0aj9kJQxkp2xj8wjg';
        }

        $this->url = rtrim($url, '/');
        $this->apiKey = $key;
    }

    private function request(string $method, string $endpoint, array $queryParams = [], $body = null, array $extraHeaders = []): array
    {
        $url = $this->url . '/rest/v1/' . ltrim($endpoint, '/');

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        
        \log_message('debug', 'Supabase Req URL: ' . $url);

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Prefer: return=representation',
        ];

        $headers = array_merge($headers, $extraHeaders);

        // Try PHP cURL extension first
        if (\function_exists('curl_init')) {
            $res = $this->requestWithCurl($method, $url, $headers, $body);
            if (!empty($res)) return $res;
        }

        // Try stream context (file_get_contents)
        $res = $this->requestWithStream($method, $url, $headers, $body);
        if (!empty($res)) return $res;

        // Ultimate fallback: system curl command (common in XAMPP/Windows if extensions are broken)
        return $this->requestWithCli($method, $url, $headers, $body);
    }

    private function requestWithCurl(string $method, string $url, array $headers, $body = null): array
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        switch (\strtoupper($method)) {
            case 'POST':
                \curl_setopt($ch, CURLOPT_POST, true);
                if ($body !== null) {
                    \curl_setopt($ch, CURLOPT_POSTFIELDS, \json_encode($body));
                }
                break;
            case 'PATCH':
                \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if ($body !== null) {
                    \curl_setopt($ch, CURLOPT_POSTFIELDS, \json_encode($body));
                }
                break;
            case 'DELETE':
                \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = \curl_exec($ch);
        $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = \curl_error($ch);
        \curl_close($ch);

        if ($error) {
            \log_message('debug', 'Supabase cURL extension failed: ' . $error);
            return [];
        }

        return $this->handleResponse($response, $httpCode);
    }

    private function requestWithStream(string $method, string $url, array $headers, $body = null): array
    {
        $headerString = implode("\r\n", $headers);

        $opts = [
            'http' => [
                'method'  => strtoupper($method),
                'header'  => $headerString,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        if ($body !== null && in_array(strtoupper($method), ['POST', 'PATCH', 'PUT'])) {
            $opts['http']['content'] = json_encode($body);
        }

        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            \log_message('debug', 'Supabase stream failed for: ' . $url);
            return [];
        }

        $httpCode = 200;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $h) {
                if (preg_match('/^HTTP\/\S+\s+(\d+)/', $h, $m)) {
                    $httpCode = (int) $m[1];
                }
            }
        }

        return $this->handleResponse($response, $httpCode);
    }

    private function requestWithCli(string $method, string $url, array $headers, $body = null): array
    {
        $configContent = "insecure\n"; // Bypasses SSL verification issues
        $configContent .= "silent\n";
        $configContent .= "request = " . strtoupper($method) . "\n";
        $configContent .= "url = \"" . str_replace('"', '\"', $url) . "\"\n";
        
        foreach ($headers as $h) {
            $configContent .= "header = \"" . str_replace('"', '\"', $h) . "\"\n";
        }

        $tmpConfig = tempnam(sys_get_temp_dir(), 'supac');
        file_put_contents($tmpConfig, $configContent);

        $cmd = 'curl.exe -K ' . escapeshellarg($tmpConfig);
        $cmd .= ' ' . escapeshellarg($url) . ' 2>&1';
        
        $tmpBody = null;
        if ($body !== null) {
            $tmpBody = tempnam(sys_get_temp_dir(), 'supab');
            if (is_array($body)) {
                file_put_contents($tmpBody, json_encode($body));
                $cmd .= ' -d "@' . $tmpBody . '"';
            } else {
                // For binary data (like file uploads)
                file_put_contents($tmpBody, $body);
                $cmd .= ' --data-binary "@' . $tmpBody . '"';
            }
        }

        $response = shell_exec($cmd);
        
        if (file_exists($tmpConfig)) unlink($tmpConfig);
        if ($tmpBody && file_exists($tmpBody)) unlink($tmpBody);

        if ($response === null) {
            \log_message('error', 'Supabase CLI curl -K failed completely.');
            return [];
        }

        $decoded = json_decode($response, true);
        if ($decoded === null && !empty($response)) {
            // If response is not JSON, it might be a curl error message
            \log_message('error', 'Supabase CLI curl error: ' . $response);
            return [];
        }

        return $decoded ?? [];
    }

    private function handleResponse(string $response, int $httpCode): array
    {
        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            \log_message('error', 'Supabase API error [' . $httpCode . ']: ' . $response);
            return [];
        }

        return $decoded ?? [];
    }

    public function from(string $table): SupabaseQuery
    {
        return new SupabaseQuery($this, $table);
    }

    public function executeQuery(string $method, string $table, array $queryParams = [], $body = null, array $extraHeaders = []): array
    {
        return $this->request($method, $table, $queryParams, $body, $extraHeaders);
    }

    public function executeStorageRequest(string $method, string $url, array $extraHeaders = [], $body = null): array
    {
        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
        ];

        $headers = array_merge($headers, $extraHeaders);

        return $this->requestWithCli($method, $url, $headers, $body);
    }

    public function storage(string $bucket): SupabaseStorage
    {
        return new SupabaseStorage($this, $bucket);
    }
}

class SupabaseStorage
{
    private SupabaseClient $client;
    private string $bucket;

    public function __construct(SupabaseClient $client, string $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    public function upload(string $path, string $filePath, string $contentType = 'image/jpeg'): array
    {
        $binaryData = file_get_contents($filePath);
        $endpoint = '/storage/v1/object/' . $this->bucket . '/' . ltrim($path, '/');
        
        // Manual construction of storage URL because request() adds /rest/v1/
        $url = rtrim(env('SUPABASE_URL'), '/') . $endpoint;
        
        $headers = [
            'Content-Type: ' . $contentType
        ];

        return $this->client->executeStorageRequest('POST', $url, $headers, $binaryData);
    }

    public function getPublicUrl(string $path): string
    {
        return rtrim(env('SUPABASE_URL'), '/') . '/storage/v1/object/public/' . $this->bucket . '/' . ltrim($path, '/');
    }
}

class SupabaseQuery
{
    private SupabaseClient $client;
    private string $table;
    private array $queryParams = [];
    private string $select = '*';
    private array $filters = [];
    private ?string $orderBy = null;
    private ?int $limitVal = null;

    public function __construct(SupabaseClient $client, string $table)
    {
        $this->client = $client;
        $this->table = $table;
    }

    public function select(string $columns = '*'): self
    {
        $this->select = $columns;
        return $this;
    }

    public function eq(string $column, $value): self
    {
        $this->filters[] = $column . '=eq.' . $value;
        return $this;
    }

    public function in(string $column, array $values): self
    {
        $this->filters[] = $column . '=in.(' . implode(',', $values) . ')';
        return $this;
    }

    public function ilike(string $column, string $pattern): self
    {
        $this->filters[] = $column . '=ilike.' . $pattern;
        return $this;
    }

    public function order(string $column, bool $ascending = true): self
    {
        $this->orderBy = $column . '.' . ($ascending ? 'asc' : 'desc');
        return $this;
    }

    public function limit(int $count): self
    {
        $this->limitVal = $count;
        return $this;
    }

    public function get(): array
    {
        $params = ['select' => $this->select];

        foreach ($this->filters as $filter) {
            $parts = explode('=', $filter, 2);
            $params[$parts[0]] = $parts[1];
        }

        if ($this->orderBy) {
            $params['order'] = $this->orderBy;
        }

        if ($this->limitVal !== null) {
            $params['limit'] = $this->limitVal;
        }

        return $this->client->executeQuery('GET', $this->table, $params);
    }

    public function insert(array $data): array
    {
        return $this->client->executeQuery('POST', $this->table, [], $data);
    }

    public function update(array $data): array
    {
        $params = [];
        foreach ($this->filters as $filter) {
            $parts = explode('=', $filter, 2);
            $params[$parts[0]] = $parts[1];
        }

        return $this->client->executeQuery('PATCH', $this->table, $params, $data);
    }

    public function delete(): array
    {
        $params = [];
        foreach ($this->filters as $filter) {
            $parts = explode('=', $filter, 2);
            $params[$parts[0]] = $parts[1];
        }

        return $this->client->executeQuery('DELETE', $this->table, $params);
    }
}
