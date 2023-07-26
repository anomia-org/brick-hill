<?php

namespace App\Helpers;

use AsyncAws\Lambda\LambdaClient;

class Renderer
{
    private $client;

    public function __construct()
    {
        $this->client = new LambdaClient([
            'region' => 'us-east-1'
        ]);
    }

    protected function invokeRenderer($payload)
    {
        $lambdaReturn = $this->client->invoke([
            'FunctionName' => 'renderer',
            'Payload' => json_encode($payload)
        ]);
        return json_decode($lambdaReturn->getPayload());
    }

    public function newPreview($object)
    {
        $payload = [
            'RenderType' => 2,
            'PreviewObject' => $object,
            'Size' => 256
        ];
        $data = $this->invokeRenderer($payload);

        return $data->Image;
    }
}
