<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class Membership
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the membership",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="userId",
     *      description="Owner of the membership",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    public $user_id;

    /**
     *  @OA\Property(
     *      title="membership",
     *      description="Type of membership",
     *      format="int64",
     *      example=4
     *  )
     *
     * @var integer
     */
    private $membership;

    /**
     *  @OA\Property(
     *      title="date",
     *      description="Date that the membership started",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $date;

    /**
     *  @OA\Property(
     *      title="length",
     *      description="Length of the membership",
     *      format="int64",
     *      example=43800
     *  )
     *
     * @var integer
     */
    public $length;

    /**
     *  @OA\Property(
     *      title="active",
     *      description="Bool if membership is currently active",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    public $active;

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