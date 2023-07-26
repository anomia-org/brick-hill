<?php

namespace App\Virtual\Models\Errors;

/**
 * @OA\Schema()
 */

class DefaultError
{
    /**
     *  @OA\Property(type="object",
     *      @OA\Property(
     *          property="message",
     *          description="Error message",
     *          example="Record not found"
     *      ),
     *      @OA\Property(
     *          property="prettyMessage",
     *          description="Error message shown to users",
     *          example="Sorry, something went wrong"
     *      )
     *  )
     *
     * @var string
     */
    private $error;
}