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
        list($message) = $this->redisClientBuilder->build()->zRevRangeByScore(
            sprintf('salesforce_outbound_messages:%s', $channelName),
            PHP_INT_MAX,
            0,
            ['limit' => [0, 1]]
        );

        return base64_decode($message);
    }
}