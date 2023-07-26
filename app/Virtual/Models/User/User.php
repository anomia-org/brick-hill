<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class User
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

    /**
     *  @OA\Property(
     *      title="description",
     *      description="Description of the user",
     *      format="string",
     *      example="Welcome to Brick Hill!"
     *  )
     *
     * @var string
     */
    private $description;

    /**
     *  @OA\Property(
     *      type="array",
     *      @OA\Items(ref="#components/schemas/Award")
     *  )
     *
     * @var array
     */
    public $awards;

    /**
     *  @OA\Property(
     *      type="array",
     *      @OA\Items(ref="#components/schemas/Status")
     *  )
     *
     * @var array
     */
    public $status;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/Membership"
     *  )
     *
     * @var object
     */
    public $membership;

    /**
     *  @OA\Property(
     *      title="lastOnline",
     *      description="Date of last online",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $last_online;

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
}
