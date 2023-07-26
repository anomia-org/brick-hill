<?php

namespace App\Virtual\Models\User;

/**
 * @OA\Schema()
 */

class Award
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the award",
     *      format="int64",
     *      example=0
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="userId",
     *      description="Owner of the award",
     *      format="int64",
     *      example=1003
     *  )
     *
     * @var integer
     */
    private $user_id;

    /**
     *  @OA\Property(
     *      title="awardId",
     *      description="Type of the award",
     *      format="int64",
     *      example=3
     *  )
     *
     * @var integer
     */
    private $award_id;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/AwardType"
     *  )
     *
     * @var object
     */
    private $award;
}