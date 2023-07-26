<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Clan\Clan;

class AdminClanController extends Controller
{
	public function scrubClanImage(Clan $clan) {
		$clan->latestAsset()->update([
			'is_approved' => 0,
			'is_pending' => 0
		]);

		return back();
	}

	public function scrubClanDesc(Clan $clan) {
		$clan->description = '[ Content Removed ]';
		$clan->timestamps = false;
		$clan->save();

		return back();
	}

	public function scrubClanName(Clan $clan) {
		$clan->title = '[ Content Removed ]';
		$clan->timestamps = false;
		$clan->save();

		return back();
	}

	public function scrubClanTag(Clan $clan) {
		$clan->tag = 'CtRm';
		$clan->timestamps = false;
		$clan->save();

		return back();
	}
}
