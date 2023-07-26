<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Clan\{
    Clan
};

class ClanAPIController extends Controller
{
    public function clan() {
        APIParams(['id']);

        return Clan::select([
            'id',
            'owner_id',
            'tag',
            'title',
            'created_at'
        ])->findOrFail(request('id'));
    }

    public function ranks() {
        APIParams(['id']);

        return Clan::findOrFail(request('id'))->ranks()->select([
            'name'
        ])->get()->pluck('name');
    }

    public function clanMember() {
        APIParams(['id', 'user']);

        return Clan::findOrFail(request('id'))->members()->where('user_id', request('user'))->with(['user'])->firstOrFail()->append('rank_data')->makeHidden(['clan']);
    }
}
