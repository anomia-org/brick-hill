<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class UserSmall
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the user",
     *      format="int64",
     *      example=1003
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="username",
     *      description="Username of the user",
     *      format="string",
     *      example="Brick Hill"
     *  )
     *
     * @var string
     */
    private $username;
}