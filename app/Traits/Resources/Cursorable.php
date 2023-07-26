<?php

namespace App\Traits\Resources;

use App\Helpers\CursorPaginator;

use Illuminate\Http\Resources\Json\ResourceCollection;

trait Cursorable
{
    /**
     * Returns the collection data with given through the paginator
     *
     * @param  CursorPaginator $paginator
     * @return ResourceCollection<mixed>
     */
    public static function paginateCollection(CursorPaginator $paginator): ResourceCollection
    {
        return self::collection($paginator->items())->additional($paginator->pages());
    }
}
