<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{
    Log
};

use Carbon\Carbon;

use App\Models\User\Email\InvalidEmail;
use App\Models\Set\ClientBuild;

class WebhookController extends Controller
{
    public function handleBounceNotifs(Request $request)
    {
        $message = json_decode($request->json('Message'), true);
        if ($request->json('Type') == 'SubscriptionConfirmation') {
            Log::info($request->json()->all());
            return;
        }

        if ($request->json('Type') == 'Notification' && $message['notificationType'] == 'Bounce') {
            $bounce = $message['bounce'];
            foreach ($bounce['bouncedRecipients'] as $bounced) {
                InvalidEmail::firstOrCreate(['email' => $bounced['emailAddress']])->touch();
            }
        }

        return [
            'status' => 200,
            'message' => 'success'
        ];
    }

    /**
     * Handle requests coming from GitLab CI indicating a new client pipeline
     * 
     * @param Request $request 
     * @return void 
     */
    public function pipelineHook(Request $request)
    {
        $attributes = $request->object_attributes;

        // only care about successful pipelines
        if ($attributes['status'] != 'success')
            return;

        // web run builds are only used as artifacts
        if ($attributes['status'] == 'web')
            return;

        switch ($request->headers->get('X-Gitlab-Token')) {
            case /* installer pipeline */ 'secretsecretsecret':
                // we only want tagged pipelines
                if (!$attributes['tag'])
                    return;

                ClientBuild::create([
                    'tag' => $attributes['ref'],
                    'is_release' => 0,
                    'is_installer' => 1
                ]);
                break;
            case /* main client pipeline */ 'secretsecretsecret1':
                $isRelease = $attributes['tag'];
                $tag = $attributes['ref'];

                if (!$isRelease) {
                    $tag = substr($attributes['sha'], 0, 8);
                }

                ClientBuild::create([
                    'tag' => $tag,
                    'is_release' => $isRelease,
                    'is_installer' => 0
                ]);
                break;
        }

        return;
    }
}
