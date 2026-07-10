<?php

namespace Classes;

/**
 * @author Tyrone malocon
 * @description: Tyrux REST for backend
 */
class Tyrux
{
    private static $baseUrl = '';
    private static $lastError = null;
    private static $statusCode;

    public static function setBaseUrl($url)
    {
        self::$baseUrl = rtrim($url, '/');
    }

    public static function request($method, $options)
    {
        $url = isset($options['url']) ? $options['url'] : '';
        $headers = isset($options['headers']) ? $options['headers'] : [];
        $data = isset($options['data']) ? $options['data'] : [];
        $timeout = isset($options['timeout']) ? $options['timeout'] : 30; // Add timeout option

        $ch = curl_init();
        $fullUrl = self::$baseUrl . $url;

        $curlOptions = [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => self::formatHeaders($headers),
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5, // Connection timeout
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            // Add these for debugging
            CURLOPT_VERBOSE => false,
        ];

        // Enable compression to reduce data transfer
        $curlOptions[CURLOPT_ENCODING] = '';

        if (!empty($data) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            $jsonData = json_encode($data);
            $curlOptions[CURLOPT_POSTFIELDS] = $jsonData;
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($jsonData);
        }

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        self::$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Debug: Check total time
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        error_log("CURL total time: " . $totalTime . " seconds");

        $error = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($error) {
            self::$lastError = "Error $errno: $error";
            return null;
        }

        return json_decode($response, true);
    }

    public static function lastError()
    {
        return self::$lastError;
    }

    public static function statusCode()
    {
        return self::$statusCode;
    }

    public static function get($options)
    {
        return self::request('GET', $options);
    }

    public static function post($options)
    {
        return self::request('POST', $options);
    }

    public static function put($options)
    {
        return self::request('PUT', $options);
    }

    public static function patch($options)
    {
        return self::request('PATCH', $options);
    }

    public static function delete($options)
    {
        return self::request('DELETE', $options);
    }

    private static function formatHeaders($headers)
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "$key: $value";
        }
        return $formatted;
    }
}
