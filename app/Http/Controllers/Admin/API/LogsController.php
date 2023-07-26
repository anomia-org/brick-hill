<?php

namespace App\Http\Controllers\Admin\API;

use App\Exceptions\Custom\InvalidDataException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;

use App\Http\Resources\Admin\LogResource;
use App\Models\User\Admin\AdminLog;
use App\Models\User\User;

class LogsController extends Controller
{
    /**
     * Returns cursorable list of logs where the associated user_id has equal or less power than the authenticated user
     * This is to hide actions above them, although perhaps thats not necessary?
     * 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     */
    public function listLogs(Request $request): mixed
    {
        $paginator = AdminLog::with('user:id,username')
            ->whereDoesntHave('user', function (Builder $q) {
                $q->where('power', '>', Auth::user()->power);
            });

        if ($request->filled('user_id')) {
            $user = User::findOrFail($request->user_id);

            if ($user->power > Auth::user()->power) {
                throw new InvalidDataException("User given has higher power than you");
            }

            $paginator->where('user_id', $user->id);
        }

        return LogResource::paginateCollection($paginator->paginateByCursor(['id' => 'desc']));
    }
}
