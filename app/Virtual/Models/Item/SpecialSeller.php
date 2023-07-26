<?php

namespace App\Virtual\Models\Item;

/**
 * @OA\Schema()
 */

class SpecialSeller
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
    private $crate_id;

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
     *      title="bucks",
     *      description="Price of the reseller",
     *      format="int64",
     *      example=400
     *  )
     *
     * @var integer
     */
    private $bucks;
}