<?php

namespace App\Virtual\Models\Item;

/**
 * @OA\Schema()
 */

class ItemV2
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the item",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/UserSmall"
     *  )
     *
     * @var string
     */
    private $creator;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/ItemType"
     *  )
     *
     * @var object
     */
    private $type;

    /**
     *  @OA\Property(
     *      property="tags",
     *      @OA\Items(ref="#components/schemas/Tag"),
     *      type="array"
     *  )
     *
     * @var array
     */
    private $tags;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/Event"
     *  )
     *
     * @var object|null
     */
    private $event;

    /**
     *  @OA\Property(
     *      title="seriesId",
     *      description="Id of the item series",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer|null
     */
    private $series_id;

    /**
     *  @OA\Property(
     *      title="isPublic",
     *      description="Bool for if the item is publically available",
     *      format="bool",
     *      example=true
     *  )
     *
     * @var bool
     */
    private $is_public;

    /**
     *  @OA\Property(
     *      title="name",
     *      description="Name",
     *      format="string",
     *      example="brick-luke's Jacket"
     *  )
     *
     * @var string
     */
    private $name;

    /**
     *  @OA\Property(
     *      title="description",
     *      description="Description",
     *      format="string",
     *      example="Everyone deserves to look cool!"
     *  )
     *
     * @var string
     */
    private $description;

    /**
     *  @OA\Property(
     *      title="bits",
     *      description="Price of item in bits",
     *      format="integer",
     *      example=0
     *  )
     *
     * @var integer
     */
    private $bits;

    /**
     *  @OA\Property(
     *      title="bucks",
     *      description="Price of item in bucks",
     *      format="integer",
     *      example=0
     *  )
     *
     * @var integer
     */
    private $bucks;

    /**
     *  @OA\Property(
     *      title="offsale",
     *      description="Bool for if the item is offsale",
     *      format="bool",
     *      example=false
     *  )
     *
     * @var bool
     */
    private $offsale;

    /**
     *  @OA\Property(
     *      title="specialEdition",
     *      description="Bool for special edition",
     *      format="bool",
     *      example=false
     *  )
     *
     * @var bool
     */
    private $special_edition;

    /**
     *  @OA\Property(
     *      title="special",
     *      description="Bool for special",
     *      format="bool",
     *      example=false
     *  )
     *
     * @var bool
     */
    private $special;

    /**
     *  @OA\Property(
     *      title="stock",
     *      description="Stock for a special item",
     *      format="integer",
     *      example=0
     *  )
     *
     * @var integer
     */
    private $stock;

    /**
     *  @OA\Property(
     *      title="timer",
     *      description="Bool for if timer is active",
     *      format="bool",
     *      example=false
     *  )
     *
     * @var bool
     */
    private $timer;

    /**
     *  @OA\Property(
     *      title="timerDate",
     *      description="Time which the item will go offsale. Only used when timer is true",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    private $timer_date;

    /**
     *  @OA\Property(
     *      title="averagePrice",
     *      description="Average price for a special item. Only available when special or special_edition is true",
     *      format="integer",
     *      example=80
     *  )
     *
     * @var integer
     */
    private $average_price;

    /**
     *  @OA\Property(
     *      title="stockLeft",
     *      description="Amount of stock left for a special edition item. Only available when special_edition is true",
     *      format="integer",
     *      example=0
     *  )
     *
     * @var integer
     */
    private $stock_left;

    /**
     *  @OA\Property(
     *      title="thumbnail",
     *      description="URL for item thumbnail",
     *      format="string",
     *      example="https://brkcdn.com/v2/images/shop/thumbnails/a9d071bf-8a50-5ebb-8de5-eb34647b6673.png"
     *  )
     *
     * @var string
     */
    private $thumbnail;

    /**
     *  @OA\Property(
     *      title="createdAt",
     *      description="Created at",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     *  @OA\Property(
     *      title="updatedAt",
     *      description="Updated at",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    private $updated_at;
}
