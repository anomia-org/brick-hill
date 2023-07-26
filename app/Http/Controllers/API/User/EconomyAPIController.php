<?php

namespace App\Http\Controllers\API\User;

use App\Exceptions\Custom\InvalidDataException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User\User;
use App\Models\Economy\Purchase;

use App\Http\Resources\User\Economy\PurchaseResource;

class EconomyAPIController extends Controller
{
    public function transactions(User $user, Request $request)
    {
        $query = match ($request->type) {
            'purchases' => $user->purchases(),
            'sales' => Purchase::soldBy($user->id),
            default => throw new InvalidDataException
        };
        $paginator = $query->with(['seller', 'purchaser', 'product.productable'])->paginateByCursor(['id' => 'desc']);
        return PurchaseResource::paginateCollection($paginator);
    }
}
