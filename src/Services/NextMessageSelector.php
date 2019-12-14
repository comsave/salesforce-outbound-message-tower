<?php

namespace App\Services;

use App\Services\Builder\RedisClientBuilder;

class NextMessageSelector
{
    /** @var RedisClientBuilder */
    private $redisClientBuilder;

    public function __construct(RedisClientBuilder $redisClientBuilder)
    {
        $this->redisClientBuilder = $redisClientBuilder;
    }

    public function nextMessage(string $channelName): ?string
    {
        $scanResult = $this->redisClientBuilder->build()->zRangeByScore(
            sprintf('salesforce_outbound_messages:%s', $channelName),
            0,
            PHP_INT_MAX,
            ['limit' => [0, 1]]
        );

        if(!$scanResult){
            return null;
        }

        list($message) = $scanResult;

        return base64_decode(preg_replace('/^[a-z0-9]{15,18}:/i', '', $message));
    }
}