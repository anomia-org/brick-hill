<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class Crate
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the crate",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="serial",
     *      description="Serial of the crate",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    public $serial;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/UserSmall"
     *  )
     *
     * @var string
     */
    private $user;

    /**
     *  @OA\Property(
     *      title="createdAt",
     *      description="Created at",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $created_at;

    /**
     *  @OA\Property(
     *      title="updatedAt",
     *      description="Updated at",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $updated_at;
}