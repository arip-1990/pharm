<?php

namespace App\Services\IpInfo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Cache;

class IpInfo
{
    const API_URL = 'https://ipinfo.io';
    const STATUS_CODE_QUOTA_EXCEEDED = 429;
    const REQUEST_TIMEOUT_DEFAULT = 5; // seconds

    const BATCH_MAX_SIZE = 1000;
    const BATCH_TIMEOUT = 5; // seconds

    public string $accessToken;
    public array $countries;
    public array $euCountries;
    protected Client $httpClient;

    public function __construct()
    {
        $this->accessToken = config('ipInfo.accessToken');
        $this->httpClient = new Client([
            'http_errors' => false,
            'headers' => $this->buildHeaders(),
            'timeout' => self::REQUEST_TIMEOUT_DEFAULT
        ]);
        $this->countries = config('ipInfo.countries');
        $this->euCountries = config('ipInfo.eu');
    }

    public function getDetails(string $ipAddress = null): Details
    {
        $responseDetails = $this->getRequestDetails($ipAddress);
        return $this->formatDetailsObject($responseDetails);
    }

    public function getBatchDetails(
        array $urls,
        int $batchSize = 0,
        int $batchTimeout = self::BATCH_TIMEOUT,
        bool $filter = false
    ): array {
        $lookupUrls = [];
        $results = [];

        // no items?
        if (count($urls) == 0) return $results;

        // clip batch size.
        if ($batchSize <= 0 || $batchSize > self::BATCH_MAX_SIZE) {
            $batchSize = self::BATCH_MAX_SIZE;
        }

        foreach ($urls as $url) {
            $cachedRes = Cache::get($this->cacheKey($url));
            if ($cachedRes != null)
                $results[$url] = $cachedRes;
            else
                $lookupUrls[] = $url;
        }

        // everything cached? exit early.
        if (count($lookupUrls) == 0) return $results;

        // prepare each batch & fire it off asynchronously.
        $apiUrl = self::API_URL . "/batch";
        if ($filter) $apiUrl .= '?filter=1';

        $promises = [];
        $totalBatches = ceil(count($lookupUrls) / $batchSize);
        for ($i = 0; $i < $totalBatches; $i++) {
            $start = $i * $batchSize;
            $batch = array_slice($lookupUrls, $start, $batchSize);
            $promise = $this->httpClient->postAsync($apiUrl, [
                'body' => json_encode($batch),
                'timeout' => $batchTimeout
            ])->then(function ($resp) use (&$results) {
                $batchResult = json_decode($resp->getBody(), true);
                foreach ($batchResult as $k => $v) $results[$k] = $v;
            });
            $promises[] = $promise;
        }

        // wait for all batches to finish.
        Promise\Utils::settle($promises)->wait();

        // cache any new results.
        foreach ($lookupUrls as $url) {
            if (array_key_exists($url, $results))
                Cache::add($this->cacheKey($url), $results[$url]);
        }

        return $results;
    }

    public function formatDetailsObject($details = []): Details
    {
        $country = $details['country'] ?? null;
        $details['country_name'] = $this->countries[$country] ?? null;
        $details['is_eu'] = in_array($country, $this->euCountries);

        if (array_key_exists('loc', $details)) {
            $coords = explode(',', $details['loc']);
            $details['latitude'] = $coords[0];
            $details['longitude'] = $coords[1];
        } else {
            $details['latitude'] = null;
            $details['longitude'] = null;
        }

        return new Details($details);
    }

    public function getRequestDetails(string $ip_address): array
    {
        $cachedRes = Cache::get($this->cacheKey($ip_address));
        if ($cachedRes != null) return $cachedRes;

        $url = self::API_URL;
        if ($ip_address) $url .= "/$ip_address";

        try {
            $response = $this->httpClient->request('GET', $url);
        } catch (GuzzleException $e) {
            throw new IpInfoException($e->getMessage());
        } catch (\Exception $e) {
            throw new IpInfoException($e->getMessage());
        }

        if ($response->getStatusCode() == self::STATUS_CODE_QUOTA_EXCEEDED) {
            throw new IpInfoException('IPinfo request quota exceeded.');
        } elseif ($response->getStatusCode() >= 400) {
            throw new IpInfoException('Exception: ' . json_encode([
                    'status' => $response->getStatusCode(),
                    'reason' => $response->getReasonPhrase(),
                ]));
        }

        $raw_details = json_decode($response->getBody(), true);
        Cache::add($this->cacheKey($ip_address), $raw_details);

        return $raw_details;
    }

    public function getMapUrl(array $ips): string
    {
        $url = sprintf("%s/map?cli=1", self::API_URL);

        try {
            $response = $this->httpClient->request('POST', $url, ['json' => $ips]);
        } catch (GuzzleException $e) {
            throw new IpInfoException($e->getMessage());
        } catch (\Exception $e) {
            throw new IpInfoException($e->getMessage());
        }

        $res = json_decode($response->getBody(), true);
        return $res['reportUrl'];
    }

    private function buildHeaders(): array
    {
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];

        if ($this->accessToken) $headers['authorization'] = "Bearer {$this->accessToken}";

        return $headers;
    }

    private function cacheKey(string $k): string
    {
        return sprintf('%s:%s', $k, 1);
    }
}
