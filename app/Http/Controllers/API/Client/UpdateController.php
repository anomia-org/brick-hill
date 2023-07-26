<?php

namespace App\Http\Controllers\API\Client;

use App\Exceptions\Custom\InvalidDataException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Storage
};

use App\Models\Set\ClientBuild;

use App\Http\Resources\Game\ClientBuildResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Redirector;

class UpdateController extends Controller
{
    /**
     * Returns latest release builds for the client
     * 
     * @return AnonymousResourceCollection 
     */
    public function releaseVersions()
    {
        return ClientBuildResource::collection(ClientBuild::where([['is_release', 1], ['is_installer', 0]])->orderBy('id', 'DESC')->limit(5)->get());
    }

    /**
     * Returns latest debug builds for the client
     * 
     * @return AnonymousResourceCollection 
     */
    public function debugVersions()
    {
        if (!Auth::user()->is_beta_tester)
            throw new \App\Exceptions\Custom\APIException("Debug builds are only accessible by beta testers");

        return ClientBuildResource::collection(ClientBuild::where([['is_release', 0], ['is_installer', 0]])->orderBy('id', 'DESC')->limit(5)->get());
    }

    /**
     * Returns a signed S3 url for the client to download a debug version
     * 
     * @param Request $request 
     * @return Redirector|RedirectResponse 
     */
    public function downloadDebug(Request $request)
    {
        if (!Auth::user()->is_beta_tester)
            throw new \App\Exceptions\Custom\APIException("Debug builds are only accessible by beta testers");

        $build = ClientBuild::where('tag', $request->tag)->firstOrFail();

        return redirect(Storage::disk('client_s3')->temporaryUrl(
            "debug/{$build->tag}/brick-hill-{$request->os}.zip",
            now()->addMinutes(5)
        ));
    }

    /**
     * Returns a redirect to the latest installer version
     * 
     * @param \Illuminate\Http\Request $request 
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse 
     */
    public function downloadInstaller(Request $request)
    {
        $getLatestInstaller = ClientBuild::where('is_installer', 1)->orderBy('id', 'DESC')->firstOrFail();

        $filename = match ($request->os) {
            'windows' => 'BrickHill.exe',
            'mac' => 'BrickHill.dmg',
            'linux' => 'BrickHill.tar.gz',
            default => throw new InvalidDataException()
        };

        return redirect("https://downloads.brkcdn.com/installer/{$getLatestInstaller->tag}/{$filename}");
    }
}
