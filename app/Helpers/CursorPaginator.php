<?php

namespace App\Helpers;

use App\Exceptions\Custom\InvalidDataException;

use Illuminate\Support\Collection;

class CursorPaginator
{
    protected Collection $items;
    protected ?array $nextCursor;
    protected ?array $previousCursor;

    public function __construct(Collection $items, array $nextCursor = null, array $previousCursor = null)
    {
        $this->items = $items;
        $this->nextCursor = $nextCursor;
        $this->previousCursor = $previousCursor;
    }

    public static function isCursorValid(string $cursor): bool
    {
        $cursor = self::decodeCursor(request('cursor'));

        if (strlen(request('cursor')) == 0)
            return true;

        // if its an array
        // if there are the required `type` and `cursor` keys in that array
        // if the value of `type` is either `next` or `previous`
        // if `cursor` is also an array that doesnt contain NULL, strictly typed
        return  is_array($cursor)
            && (array_key_exists('type', $cursor) && array_key_exists('cursor', $cursor))
            && (in_array($cursor['type'], ['next', 'previous']) && is_array($cursor['cursor']) && array_search(NULL, $cursor['cursor'], true) === false);
    }

    public static function currentFullCursor(): array
    {
        if (!request('cursor') || strlen(request('cursor')) == 0)
            return [];

        if (!self::isCursorValid(request('cursor')))
            throw new InvalidDataException('Invalid cursor used');

        return self::decodeCursor(request('cursor'));
    }

    public static function currentCursor(): array
    {
        return self::currentFullCursor()['cursor'] ?? [];
    }

    public static function currentCursorType(): string
    {
        return self::currentFullCursor()['type'] ?? 'next';
    }

    public static function encodeCursor(array $cursor, string $type): array
    {
        return (array) base64_encode(json_encode(['type' => $type, 'cursor' => array_values($cursor)]));
    }

    public static function decodeCursor(string $cursor): array
    {
        return (array) json_decode(base64_decode($cursor));
    }

    public function load(string ...$values): self
    {
        // not sure how this should be formatted
        /** @phpstan-ignore-next-line */
        $this->items->load($values);

        return $this;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function mapItems(callable $callback): self
    {
        $this->items = $this->items->map($callback);

        return $this;
    }

    public function data(): array
    {
        return [
            'data' => $this->items(),
            'next_cursor' => $this->nextCursor ? $this->encodeCursor($this->nextCursor, 'next')[0] : null,
            'previous_cursor' => (count($this->currentCursor()) > 0 && !is_null($this->previousCursor)) ? $this->encodeCursor($this->previousCursor, 'previous')[0] : null
        ];
    }

    public function pages(): array
    {
        return [
            'next_cursor' => $this->nextCursor ? $this->encodeCursor($this->nextCursor, 'next')[0] : null,
            'previous_cursor' => (count($this->currentCursor()) > 0 && !is_null($this->previousCursor)) ? $this->encodeCursor($this->previousCursor, 'previous')[0] : null
        ];
    }
}
