<?php

namespace App\Virtual\Models\Item;

/**
 * @OA\Schema()
 */

class Event
{
    /**
     *  @OA\Property(
     *      title="id",
     *      description="ID of the event",
     *      format="int64",
     *      example=1
     *  )
     *
     * @var integer
     */
    private $id;

    /**
     *  @OA\Property(
     *      title="type",
     *      description="Name of the event",
     *      format="string",
     *      example="Egg Hunt 2022"
     *  )
     *
     * @var string
     */
    private $name;

    /**
     *  @OA\Property(
     *      title="startDate",
     *      description="Start time of the event",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $start_date;

    /**
     *  @OA\Property(
     *      title="endDate",
     *      description="End time of the event",
     *      format="date-time"
     *  )
     *
     * @var \DateTime
     */
    public $end_date;
}
