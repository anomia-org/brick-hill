<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

use App\Models\Casts\AsCollection;

/**
 * App\Models\User\Avatar
 *
 * @property int $user_id
 * @property \Illuminate\Support\Collection|null $items
 * @property array $variations
 * @property \Illuminate\Support\Collection|null $colors
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $items_list
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar whereColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Avatar whereVariations($value)
 * @mixin \Eloquent
 */
class Avatar extends Model
{
	public $primaryKey = 'user_id';

	public $casts = [
		'items' => AsCollection::class,
		'variations' => 'array',
		'colors' => AsCollection::class
	];

	protected $fillable = [
		'user_id',
		'variations',

		'colors',
		'colors->head', 'colors->torso',
		'colors->left_arm', 'colors->right_arm',
		'colors->right_arm', 'colors->right_leg',

		'items',
		'items->face', 'items->head', 'items->tool', 'items->figure',
		'items->pants', 'items->shirt', 'items->tshirt',

		'items->hats->0', 'items->hats->1', 'items->hats->2', 'items->hats->3', 'items->hats->4',
		'items->clothing->0', 'items->clothing->1', 'items->clothing->2', 'items->clothing->3', 'items->clothing->4'
	];

	/**
	 * Returns a list of unique item ids avatar is wearing
	 * 
	 * @return mixed 
	 */
	public function getItemsListAttribute()
	{
		return $this
			->items
			->flatten()
			->unique()
			// removes all falsey values (which can only be 0)
			->filter();
	}
}
