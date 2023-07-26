<?php

namespace App\Exceptions\Custom\Internal;

use App\Exceptions\Custom\AbstractAPIException;

/**
 * Thrown when the application finds invalid data that would have been generated by internal systems.
 * 
 * @package App\Exceptions\Custom\Internal
 */
class InvalidDataException extends AbstractAPIException
{
    /**
     * This should never happen so should be reported
     * 
     * @var bool
     */
    public bool $shouldBeReported = true;

    /**
     * Initialize the error
     * 
     * @param string $errorReason 
     * @return void 
     */
    public function __construct(
        protected string $errorReason = 'Error occured processing data'
    ) {
        $this->message = $errorReason;
    }

    /**
     * Return the set message
     * 
     * @return string 
     */
    public function getDetailedMessage(): string
    {
        return $this->errorReason;
    }

    /**
     * Get status code of exception
     * 
     * @return int 
     */
    public function getStatusCode(): int
    {
        return 500;
    }
}