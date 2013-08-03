<?php

namespace QueryAuth;

class Client
{
    /**
     * Sign request params
     *
     * @param  string $key    API key
     * @param  string $secret API secret
     * @param  string $method Request method (GET, POST, PUT, HEAD, etc)
     * @param  string $host   Host portion of API resource URL (including subdomain, excluding scheme)
     * @param  string $path   Path portion of API resource URL (excluding query and fragment)
     * @param  array  $params OPTIONAL Request params (query or POST fields), only needed if required by endpoint
     * @return array  Signed request params
     */
    public function getSignedRequestParams($key, $secret, $method, $host, $path, array $params = array())
    {
        $params['key'] = $key;
        $params['timestamp'] = (int) gmdate('U');

        // Ensure path is absolute
        $path = '/' . ltrim($path, '/');

        $data = $method . "\n"
            . $host . "\n"
            . $path . "\n"
            . $this->getNormalizedParameterString($params);

        $signature = \base64_encode(\hash_hmac('sha256', $data, $secret, true));

        if ($method == 'GET') {
            $signature = urlencode($signature);
        }

        $params['signature'] = $signature;

        return $params;
    }

    /**
     * Get request params as string, sorted alphabetically using strcmp
     *
     * @param  array  $params Array of request paramaters
     * @return string Normalized, rawurlencoded parameters as string
     */
    public function getNormalizedParameterString(array $params)
    {
        unset($params['signature']);
        uksort($params, 'strcmp');

        $normalized = '';

        foreach ($params as $key => $value) {
            $normalized .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
        }

        return substr($normalized, 0, -1);
    }
}
