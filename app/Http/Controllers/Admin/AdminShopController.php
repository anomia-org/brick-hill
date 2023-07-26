<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Item\Item;

class AdminShopController extends Controller
{
	public function scrubItemDesc(Item $item)
	{
		$this->authorize('update', $item->creator);

		$item->description = '[ Content Removed ]';
		$item->timestamps = false;
		$item->save();

		return back();
	}

	public function scrubItemName(Item $item)
	{
		$this->authorize('update', $item->creator);

		$item->name = '[ Content Removed ]';
		$item->timestamps = false;
		$item->save();

		return back();
	}

	public function renderItem(Item $item)
	{
		$item->thumbnails()->update([
			'expires_at' => now()
		]);

		return back();
	}
}
