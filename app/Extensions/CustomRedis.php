<?php

namespace App\Extensions;

use Redis;

class CustomRedis extends Redis
{
    public function connect(
        $host,
        $port = 6379,
        $timeout = 0.0,
        $reserved = null,
        $retryInterval = 0,
        $readTimeout = 0.0
    ) {
        $connected = false;

        $dnsResults = dns_get_record($host, DNS_SRV);

        for ($i = 0; !$connected && $i < count($dnsResults); $i++) {
            $connected = parent::connect($dnsResults[$i]['target'], $dnsResults[$i]['port'], $timeout, $reserved, $retryInterval, $readTimeout);
        }

        if (!$connected) {
            $connected = parent::connect($host, $port, $timeout, $reserved, $retryInterval, $readTimeout);
        }

        return $connected;
    }

    public function pconnect(
        $host,
        $port = 6379,
        $timeout = 0.0,
        $persistentId = null,
        $retryInterval = 0,
        $readTimeout = 0.0
    ) {
        $connected = false;

        // if in local or testing you can assume it doesnt need srv
        if (app()->environment(['local', 'testing'])) {
            // phpstan appears to be under the impression that the parent only has 5 parameters?
            // it has defaults so i could just accept that but then my intellisense reports errors because it should be 6
            // @phpstan-ignore-next-line
            return parent::pconnect($host, $port, $timeout, $persistentId, $retryInterval, $readTimeout);
        } else {
            $dnsResults = dns_get_record($host, DNS_SRV);
        }

        for ($i = 0; !$connected && $i < count($dnsResults); $i++) {
            // @phpstan-ignore-next-line
            $connected = parent::pconnect($dnsResults[$i]['target'], $dnsResults[$i]['port'], $timeout, $persistentId, $retryInterval, $readTimeout);
        }

        if (!$connected) {
            // @phpstan-ignore-next-line
            $connected = parent::pconnect($host, $port, $timeout, $persistentId, $retryInterval, $readTimeout);
        }

        return $connected;
    }
}
