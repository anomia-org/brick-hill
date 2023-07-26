<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

// deprecated do not use
class CrateV1
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
     *      title="username",
     *      description="Crate owners username",
     *      example="Brick Hill"
     *  )
     *
     * @var string
     */
    private $username;

    /**
     *  @OA\Property(
     *      title="userId",
     *      description="Crate owners user id",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    public $user_id;

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