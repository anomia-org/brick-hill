<?php

namespace App\Http\Controllers\Admin\Super\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Super\Promo\MassImport;
use App\Http\Requests\Admin\Super\Promo\NewPromo;
use App\Http\Resources\Admin\Event\PromoCodeLookupResource;
use App\Models\Event\PromoCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PromoController extends Controller
{

    /**
     * Add a global middleware to the controller to only allow one request at a time
     * 
     * @return void 
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $lock = Cache::lock('promocode_lock', 30);

            if ($lock->get()) {
                $response = $next($request);

                $lock->release();

                return $response;
            } else {
                throw new \App\Exceptions\Custom\APIException("Promo Code system can only process one request at a time. Please wait.");
            }
        });
    }

    /**
     * Creates a new promo code
     * 
     * @param NewPromo $request 
     * @return array
     */
    public function newPromo(NewPromo $request): array
    {
        PromoCode::create([
            'code' => strtolower($request->code),
            'item_id' => $request->item,
            'is_single_use' => 0,
            'expires_at' => Carbon::parse($request->date)
        ]);

        return [
            'success' => true
        ];
    }

    /**
     * Mass import multiple single use promo codes
     * 
     * @param MassImport $request 
     * @return array 
     */
    public function massImport(MassImport $request): array
    {
        $inserts = [];
        foreach ($request->codes as $code) {
            array_push($inserts, [
                'code' => $code,
                'item_id' => $request->item,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        PromoCode::insert($inserts);

        return [
            'success' => true
        ];
    }

    /**
     * Returns data on a given PromoCode
     * 
     * @param Request $request 
     * @return PromoCodeLookupResource 
     */
    public function lookupCode(Request $request)
    {
        $code = PromoCode::code($request->code)->firstOrFail();

        return new PromoCodeLookupResource($code);
    }
}
