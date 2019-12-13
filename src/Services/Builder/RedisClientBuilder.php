<?php

namespace App\Services\Builder;

class RedisClientBuilder
{
    public function build(): \Redis
    {
        $redis = new \Redis();
        $redis->pconnect('tcp://redis:6379');
        $redis->ping();

        return $redis;
    }
}