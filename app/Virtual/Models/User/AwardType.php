<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class AwardType
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the award",
     *      format="int64",
     *      example=3
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="name",
     *      description="Name of the award",
     *      format="string",
     *      example="Admin"
     *  )
     *
     * @var string
     */
    private $name;

    /**
     *  @OA\Property(
     *      title="description",
     *      description="Description of the award",
     *      format="string",
     *      example="Users with this award work for Brick Hill."
     *  )
     *
     * @var string
     */
    private $description;
}