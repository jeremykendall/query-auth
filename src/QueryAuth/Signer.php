<?php

namespace QueryAuth;

use QueryAuth\NormalizedParameterCollection;

class Signer
{
    /**
     * @var NormalizedParameterCollection
     */
    private $collection;

    public function __construct(NormalizedParameterCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Creates a signature
     * 
     * @param string $method The request method
     * @param string $host   The host
     * @param string $path   The path
     * @param string $secret The auth secret
     * @param array  $params Array of params to set into the collection
     * @return string The signature
     */
    public function createSignature($method, $host, $path, $secret, array $params)
    {
        $this->collection->setFromArray($params);

        $data = $method . "\n"
            . $host . "\n"
            . $path . "\n"
            . $this->collection->normalize();

        $signature = \base64_encode(\hash_hmac('sha256', $data, $secret, true));

        // For GET requests the signature needs to be URL encoded
        if ($method == 'GET') {
            $signature = urlencode($signature);
        }

        return $signature;
    }
}
