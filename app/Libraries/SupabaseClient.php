<?php

namespace App\Libraries;

class SupabaseClient
{
    private string $url;
    private string $apiKey;

    public function __construct()
    {
        $this->url = rtrim(env('SUPABASE_URL', ''), '/');
        $this->apiKey = env('SUPABASE_ANON_KEY', '');
    }

    private function request(string $method, string $endpoint, array $queryParams = [], $body = null, array $extraHeaders = []): array
    {
        $url = $this->url . '/rest/v1/' . ltrim($endpoint, '/');

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

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
        $cmd = 'curl.exe -s -X ' . strtoupper($method);
        
        foreach ($headers as $h) {
            $cmd .= ' -H "' . str_replace('"', '\"', $h) . '"';
        }

        if ($body !== null && in_array(strtoupper($method), ['POST', 'PATCH', 'PUT'])) {
            $jsonBody = json_encode($body);
            $tmpFile = tempnam(sys_get_temp_dir(), 'supa');
            file_put_contents($tmpFile, $jsonBody);
            $cmd .= ' -d "@' . $tmpFile . '"';
        }

        $cmd .= ' "' . $url . '"';
        
        $response = shell_exec($cmd);
        
        if (isset($tmpFile) && file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        if ($response === null) {
            \log_message('error', 'Supabase CLI curl failed completely.');
            return [];
        }

        // CLI curl output doesn't give HTTP code easily without extra flags, 
        // but Supabase usually returns JSON on success or error.
        return json_decode($response, true) ?? [];
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
        $this->filters[] = $column . '=eq.' . urlencode($value);
        return $this;
    }

    public function in(string $column, array $values): self
    {
        $this->filters[] = $column . '=in.(' . implode(',', array_map('urlencode', $values)) . ')';
        return $this;
    }

    public function ilike(string $column, string $pattern): self
    {
        $this->filters[] = $column . '=ilike.' . urlencode($pattern);
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
