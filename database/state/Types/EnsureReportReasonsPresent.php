<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureReportReasonsPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'report_reasons';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "reason"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'reason' => 'Excessive or inappropriate use of profanity'],
        ['id' => 2, 'reason' => 'Inappropriate/adult content'],
        ['id' => 3, 'reason' => 'Requesting or giving private information'],
        ['id' => 4, 'reason' => 'Engaging in third party/offsite deals'],
        ['id' => 5, 'reason' => 'Harassing/bullying other users'],
        ['id' => 6, 'reason' => 'Exploiting/scamming other users'],
        ['id' => 7, 'reason' => 'Stolen account'],
        ['id' => 8, 'reason' => 'Phishing/hacking/trading accounts'],
        ['id' => 9, 'reason' => 'Other'],
    ];
}
