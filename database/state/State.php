<?php

namespace Database\State;

use Illuminate\Support\Facades\DB;

use \App\Exceptions\Custom\Internal\InvalidDataException;
use Illuminate\Support\Collection;

class State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table;

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns;

    /**
     * Array of rows that should exist
     * 
     * @var array<array>
     */
    protected array $rows;

    /**
     * If all the rows defined are present in the database
     * Defaults to true, is set to false by $this->updateRows() if it finds a missing value
     */
    protected bool $allPresent = true;

    /**
     * The column name it should use to determine if the data is present
     * 
     * @var string
     */
    protected string $uniqueKey = "id";

    /**
     * Run the check to insert rows
     * 
     * @return void 
     */
    public function __invoke()
    {
        $this->validate();

        $this->updateRows();

        if ($this->allPresent) {
            return;
        }

        DB::table($this->table)->insertOrIgnore($this->rows);
    }

    /**
     * Determine if the given rows are valid to insert
     * 
     * @return void
     * @throws \App\Exceptions\Custom\Internal\InvalidDataException
     */
    private function validate(): void
    {
        collect($this->rows)->each(function (array $row, $key) {
            if (!is_array($row) || !array_key_exists('id', $row) || is_null($row['id'])) {
                throw new \App\Exceptions\Custom\Internal\InvalidDataException(
                    <<<'EOT'
                    Attempting to update DB state with given rows that have NULL ids. 
                      This can result in data being duplicated if the present function can't properly detect the data existing.
                    EOT
                );
            }

            if (array_keys($row) !== $this->columns) {
                // TODO: list the columns and data, maybe the class
                throw new InvalidDataException("Attempting to add rows defined with unspecified columns");
            }
        });
    }

    /**
     * Search through the DB to find rows that just need to be updated,
     * also sets allPresent to false if it finds a missing row
     * 
     * @return bool 
     */
    private function updateRows(): void
    {
        if (DB::table($this->table)->count() == 0) {
            $this->allPresent = false;
            return;
        }

        $collectedRows = collect($this->rows);

        DB::table($this->table)
            ->select($this->columns)
            ->orderBy('id')
            ->chunk(100, function (Collection $data) use ($collectedRows) {
                foreach ($data as $oldRow) {
                    // sort through the DB rows to find the one with the matching key stored in the class
                    $newRow = $collectedRows
                        ->first(
                            fn (array $findRow) =>
                            $findRow[$this->uniqueKey] === $oldRow->{$this->uniqueKey}
                        );

                    // value doesnt exist, need to insert
                    if (!$newRow) {
                        $this->allPresent = false;
                        continue;
                    }

                    // why do i need to collect these, why are they stdClasses
                    $newRow = collect($newRow);
                    $oldRow = collect($oldRow);

                    // value exists but doesnt match, need to update
                    if ($newRow->diff($oldRow)->count() > 0) {
                        DB::table($this->table)
                            ->where($this->uniqueKey, $newRow->get($this->uniqueKey))
                            ->update($newRow->all());
                    }
                }
            });
    }
}
