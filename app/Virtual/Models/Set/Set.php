<?php

namespace App\Virtual\Models\Set;

/**
 * @OA\Schema()
 */

class Set
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="Id of the set",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      ref="#components/schemas/UserSmall"
     *  )
     *
     * @var string
     */
    private $creator;

    /**
     *  @OA\Property(
     *      title="name",
     *      description="Name",
     *      format="string",
     *      example="Planegg of Mars - Egg Hunt 2018"
     *  )
     *
     * @var string
     */
    public $name;

    /**
     *  @OA\Property(
     *      title="description",
     *      description="Description",
     *      format="string",
     *      example="Explore the extraterrestrial environment with you and your friends as you compete to find all of the treasures it has to offer!"
     *  )
     *
     * @var string
     */
    public $description;

    /**
     *  @OA\Property(
     *      title="playing",
     *      description="Number of users in the set",
     *      format="integer",
     *      example=0
     *  )
     *
     * @var integer
     */
    private $playing;

    /**
     *  @OA\Property(
     *      title="visits",
     *      description="Number of visits",
     *      format="integer",
     *      example=2426
     *  )
     *
     * @var integer
     */
    private $vists;

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