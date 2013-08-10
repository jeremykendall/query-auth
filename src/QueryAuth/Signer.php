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

    public function createSignature($method, $host, $path, $secret, array $params)
    {
        $this->collection->setFromArray($params);

        $data = $method . "\n"
            . $host . "\n"
            . $path . "\n"
            . $this->collection->normalize();

        return \base64_encode(\hash_hmac('sha256', $data, $secret, true));
    }
}
