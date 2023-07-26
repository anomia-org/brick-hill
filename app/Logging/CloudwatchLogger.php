<?php

namespace App\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;

class CloudwatchLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $sdkParams = $config['aws'];

        $client = new CloudWatchLogsClient($sdkParams);

        $groupName = '/website/laravel';
        $streamName = $config['name'] ?? 'default';
        if (!app()->environment(['prod', 'production']))
            $streamName = 'local_testing';

        $handler = new CloudWatch($client, $groupName, $streamName, 14, 1);

        $logger = new Logger($streamName);
        $logger->pushHandler($handler);

        return $logger;
    }
}
