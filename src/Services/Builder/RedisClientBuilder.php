<?php

namespace App\Services\Builder;

class RedisClientBuilder
{
    public function build(): \Redis
    {
        $redis = new \Redis();
        $redis->pconnect('redis', 6379);
        $redis->ping();

        return $redis;
    }
}