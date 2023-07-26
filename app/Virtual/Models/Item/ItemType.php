<?php

namespace App\Virtual\Models\Item;

/**
 * @OA\Schema()
 */

class ItemType
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the type",
     *      format="int64",
     *      example=6
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="type",
     *      description="Type in string form",
     *      format="string",
     *      example="tshirt"
     *  )
     *
     * @var string
     */
    private $type;
}
