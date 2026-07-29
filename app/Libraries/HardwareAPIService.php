<?php
// app/Libraries/HardwareApiService.php

namespace App\Libraries;

use Config\Hardware as HardwareConfig;
use CodeIgniter\HTTP\Exceptions\HTTPException;

class HardwareApiService
{
    protected $client;
    protected string $baseUrl;
    protected string $token;

    /** Snipe-IT's hard API cap per request */
    protected const MAX_LIMIT = 500;

    public function __construct()
    {
        $config = new HardwareConfig();

        $this->baseUrl = rtrim($config->apiUrl, '/');
        $this->token   = $config->apiToken;
        $this->client  = \Config\Services::curlrequest([
            'timeout' => 10,
        ]);
    }

    /**
     * Get all hardware assigned to a specific Snipe-IT user, mapped to a clean array.
     */
    public function getHardwareByUser(int $userId): array
    {
        $rows = $this->fetchAllPages([
            'assigned_to'   => $userId,
            'assigned_type' => 'App\Models\User',
            'sort'          => 'name',
            'order'         => 'asc',
        ]);

        $totalFetched = count($rows);

        // Defensive filter — don't rely solely on the API query params.
        // Cast both sides to int since session/query values can arrive as strings.
        $rows = array_filter($rows, function ($item) use ($userId) {
            $assignedId   = $item['assigned_to']['id'] ?? null;
            $assignedType = $item['assigned_to']['type'] ?? null;

            return $assignedId !== null
                && (int) $assignedId === $userId
                && $assignedType === 'user';
        });

        $filteredCount = count($rows);

        // Flag (via log) if the API-side filter and PHP-side filter disagree —
        // useful for catching Snipe-IT instances that ignore assigned_to/assigned_type.
        if ($filteredCount !== $totalFetched) {
            log_message(
                'warning',
                "HardwareApiService: API returned {$totalFetched} rows for user {$userId}, " .
                "but only {$filteredCount} actually matched assigned_to. API-side filter may not be honored."
            );
        }

        return array_values(array_map([$this, 'mapAsset'], $rows));
    }

    /**
     * Get raw hardware data (optionally scoped to a user), unmapped, all pages combined.
     */
    public function getHardwareRaw(?int $userId = null): array
    {
        $query = [
            'sort'  => 'name',
            'order' => 'asc',
        ];

        if ($userId) {
            $query['assigned_to']   = $userId;
            $query['assigned_type'] = 'App\Models\User';
        }

        return $this->fetchAllPages($query);
    }

    /**
     * Resolve a Snipe-IT user ID from a raw search term (username, email, etc).
     * NOTE: this does fuzzy matching server-side — prefer findUserIdByUsername()
     * or findUserIdByName() below, which verify the match before returning.
     */
    public function findUserIdBySearch(string $search): ?int
    {
        $result = $this->request('GET', '/api/v1/users', [
            'search' => $search,
            'limit'  => 1,
        ]);

        return $result['rows'][0]['id'] ?? null;
    }

    /**
     * Resolve a Snipe-IT user ID by exact username match.
     */
    public function findUserIdByUsername(string $username): ?int
    {
        $username = trim($username);

        if ($username === '') {
            return null;
        }

        $result = $this->request('GET', '/api/v1/users', [
            'search' => $username,
            'limit'  => 10,
        ]);

        foreach ($result['rows'] ?? [] as $row) {
            if (strcasecmp(trim($row['username'] ?? ''), $username) === 0) {
                return $row['id'];
            }
        }

        return null;
    }

    /**
     * Resolve a Snipe-IT user ID by exact first + last name match.
     */
    public function findUserIdByName(string $firstName, string $lastName): ?int
    {
        $firstName = trim($firstName);
        $lastName  = trim($lastName);

        if ($firstName === '' && $lastName === '') {
            return null;
        }

        $result = $this->request('GET', '/api/v1/users', [
            'search' => trim("{$firstName} {$lastName}"),
            'limit'  => 10,
        ]);

        foreach ($result['rows'] ?? [] as $row) {
            $rowFirst = trim($row['first_name'] ?? '');
            $rowLast  = trim($row['last_name'] ?? '');

            if (strcasecmp($rowFirst, $firstName) === 0 && strcasecmp($rowLast, $lastName) === 0) {
                return $row['id'];
            }
        }

        return null;
    }

    /**
     * Resolve a Snipe-IT user ID by exact employee_number match.
     */
    public function findUserIdByEmployeeNumber(string $employeeNumber): ?int
    {
        $employeeNumber = trim($employeeNumber);

        if ($employeeNumber === '') {
            return null;
        }

        $result = $this->request('GET', '/api/v1/users', [
            'search' => $employeeNumber,
            'limit'  => 10,
        ]);

        foreach ($result['rows'] ?? [] as $row) {
            if (strcasecmp(trim((string) ($row['employee_number'] ?? '')), $employeeNumber) === 0) {
                return $row['id'];
            }
        }

        return null;
    }

    /**
     * Try employee number, then username, then fall back to first/last name matching.
     * Pass null for any value you don't have.
     */
    public function findUserId(
        ?string $username,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $employeeNumber = null
    ): ?int {
        if ($employeeNumber) {
            $id = $this->findUserIdByEmployeeNumber($employeeNumber);
            if ($id) {
                return $id;
            }
        }

        if ($username) {
            $id = $this->findUserIdByUsername($username);
            if ($id) {
                return $id;
            }
        }

        if ($firstName || $lastName) {
            return $this->findUserIdByName($firstName ?? '', $lastName ?? '');
        }

        return null;
    }

    /**
     * Map a raw Snipe-IT hardware row into the shape the app uses.
     */
    protected function mapAsset(array $item): array
    {
        return [
            'id'            => $item['id'] ?? null,
            'asset_tag'     => $item['asset_tag'] ?? '',
            'name'          => !empty($item['name']) ? $item['name'] : ($item['model']['name'] ?? ''),
            'model'         => $item['model']['name'] ?? '',
            'serial'        => $item['serial'] ?? '',
            'category'      => $item['category']['name'] ?? '',
            'status'        => $item['status_label']['name'] ?? '',
            'status_meta'   => $item['status_label']['status_meta'] ?? '',
            'assigned_to'   => $item['assigned_to']['name'] ?? '',
            'last_checkout' => $item['last_checkout']['formatted'] ?? null,
            'can_checkin'   => $item['available_actions']['checkin'] ?? false,
            'can_checkout'  => $item['available_actions']['checkout'] ?? false,
        ];
    }

    /**
     * Fetch every page of results for a given query, since Snipe-IT caps
     * `limit` at 500 and reports `total` in the response envelope.
     */
    protected function fetchAllPages(array $query): array
    {
        $offset   = 0;
        $allRows  = [];
        $total    = null;

        do {
            $query['limit']  = self::MAX_LIMIT;
            $query['offset'] = $offset;

            $page = $this->request('GET', '/api/v1/hardware', $query);

            $rows = $page['rows'] ?? [];
            $allRows = array_merge($allRows, $rows);

            $total  = $page['total'] ?? count($allRows);
            $offset += self::MAX_LIMIT;
        } while ($offset < $total);

        return $allRows;
    }

    protected function request(string $method, string $endpoint, array $query = []): array
    {
        $options = [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ];

        if (! empty($query)) {
            $options['query'] = $query;
        }

        try {
            $response = $this->client->request($method, $this->baseUrl . $endpoint, $options);
        } catch (\Exception $e) {
            log_message('error', 'HardwareApiService request failed: ' . $e->getMessage());
            throw new HTTPException('Unable to reach hardware API', 502);
        }

        $statusCode = $response->getStatusCode();
        $body       = json_decode($response->getBody(), true);

        if ($statusCode >= 400) {
            throw new HTTPException(
                $body['message'] ?? 'Hardware API request failed',
                $statusCode
            );
        }

        return $body ?? [];
    }
}