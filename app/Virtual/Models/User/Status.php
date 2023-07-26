<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class Status
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the status",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="ownerId",
     *      description="Creator of the status",
     *      format="int64",
     *      example=1003
     *  )
     *
     * @var integer
     */
    private $owner_id;

    /**
     *  @OA\Property(
     *      title="clanId",
     *      description="Creator of the status",
     *      format="int64",
     *      example=null
     *  )
     *
     * @var integer
     */
    private $clan_id;

    /**
     *  @OA\Property(
     *      title="type",
     *      description="Type of owner of the status",
     *      format="string",
     *      example="user"
     *  )
     *
     * @var string
     */
    private $type;

    /**
     *  @OA\Property(
     *      title="body",
     *      description="Body of the status",
     *      format="string",
     *      example="We've hit the big 10k!"
     *  )
     *
     * @var string
     */
    private $body;

    /**
     *  @OA\Property(
     *      title="date",
     *      description="Created at",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $date;
}