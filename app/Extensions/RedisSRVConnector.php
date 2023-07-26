<?php

namespace App\Extensions;

use Illuminate\Redis\Connectors\PhpRedisConnector;

use App\Extensions\CustomRedis;

class RedisSRVConnector extends PhpRedisConnector
{
    /**
     * Create the Redis client instance.
     *
     * @param  array  $config
     * @return \Redis
     *
     * @throws \LogicException
     */
    protected function createClient(array $config)
    {
        return tap(new CustomRedis, function ($client) use ($config) {
            $this->establishConnection($client, $config);

            if (!empty($config['password'])) {
                $client->auth($config['password']);
            }

            if (isset($config['database'])) {
                $client->select((int) $config['database']);
            }

            if (!empty($config['prefix'])) {
                $client->setOption(CustomRedis::OPT_PREFIX, $config['prefix']);
            }

            if (!empty($config['read_timeout'])) {
                $client->setOption(CustomRedis::OPT_READ_TIMEOUT, $config['read_timeout']);
            }

            if (!empty($config['scan'])) {
                $client->setOption(CustomRedis::OPT_SCAN, $config['scan']);
            }
        });
    }
}
