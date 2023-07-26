<?php

namespace App\Virtual\Models\Item;

/**
 * @OA\Schema()
 */

class Tag
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="ID of the tag",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="type",
     *      description="Name of the tag",
     *      format="string",
     *      example="blue"
     *  )
     *
     * @var string
     */
    private $name;
}
