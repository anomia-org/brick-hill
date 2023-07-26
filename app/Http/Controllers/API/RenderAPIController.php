<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Helpers\Assets\Creator;
use Intervention\Image\ImageManagerStatic as Image;

use App\Http\Requests\Shop\Preview;
use AsyncAws\Lambda\LambdaClient;

class RenderAPIController extends Controller
{
    public function newPreview(Preview $request, Creator $creator)
    {
        $hasTexture = ['tshirt', 'shirt', 'pants', 'hat', 'face', 'tool'];
        $hasMesh = ['hat', 'tool', 'head'];

        $defaultItems = config('site.avatar.default_items');

        $itemNode = $creator->newNode($request->type);
        if (in_array($request->type, $hasTexture) || ($request->type == 'head' && $request->has('texture'))) {
            $texture = base64_encode(Image::make($request->texture)->encode('png'));
            $itemNode->setValue('texture', "b64://{$texture}");
        } else {
            $itemNode->setValue('texture', null);
        }

        if (in_array($request->type, $hasMesh)) {
            $mesh = base64_encode(file_get_contents($request->mesh));
            $itemNode->setValue('mesh', "b64://{$mesh}");
        } else {
            $itemNode->setValue('mesh', null);
        }

        $creator->nodes = $creator->nodes[0];

        if ($request->type == "hat") {
            $defaultItems['hats'][0] = $creator;
        } else if (in_array($request->type, ['pants', 'shirt', 'tshirt'])) {
            $defaultItems['clothing'][] = $creator;
        } else {
            $defaultItems[$request->type] = $creator;
        }

        $lambdaClient = new LambdaClient([
            'region' => 'us-east-1'
        ]);

        $callLambda = $lambdaClient->invoke([
            'FunctionName' => 'renderer-client-based',
            'Payload' => json_encode([
                'AvatarJSON' => json_encode([
                    'user_id' => 1,
                    'items' => $defaultItems,
                    'colors' => [
                        'head' => 'F3B700',
                        'torso' => 'B1B1B1',
                        'left_arm' => 'F3B700',
                        'right_arm' => 'F3B700',
                        'left_leg' => 'E9EAEE',
                        'right_leg' => 'E9EAEE',
                    ]
                ]),
                'Size' => 256
            ])
        ]);

        $data = json_decode($callLambda->getPayload());

        return Image::make($data->Image)
            ->resize(256, 256)
            ->encode('webp')
            ->response('data-url');
    }
}
