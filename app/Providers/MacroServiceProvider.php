<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Http\Request;
use Laravel\Scout\Builder as ScoutBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{
    Arr,
    Collection
};

use App\Helpers\CursorPaginator;
use App\Exceptions\Custom\APIException;
use App\Exceptions\Custom\InvalidDataException;
use Illuminate\Database\Eloquent\Relations\Relation;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Request::macro('subdomain', function () {
            /** @var Request $this */
            return Arr::first(explode('.', $this->getHost()));
        });

        // cursor paginator function
        // if no columns are specified it will use the columns already defined in the order by
        // it will add a select to the function in a subquery orderby so it can get the proper data

        ScoutBuilder::macro('cursorPaginate', function () {
            /** @var ScoutBuilder $this */

            // https://github.com/nunomaduro/larastan/issues/1252
            /** @phpstan-ignore-next-line */
            $engine = $this->engine();

            $cursor = CursorPaginator::currentCursor();
            $cursorType = CursorPaginator::currentCursorType();

            if ($cursorType !== "next") {
                throw new APIException("Search API only allows next type cursors. Use the previously given value instead.");
            }

            $results = $this->model->newCollection($engine->map(
                $this,
                $raw = $engine->cursorPaginate($this, request()->get('limit'), $cursor),
                $this->model
            )->all());

            if (count($raw['hits']['hits']) == request()->get('limit')) {
                $end = end($raw['hits']['hits']);
                if ($end && !array_key_exists('sort', $end)) {
                    throw new APIException("Query must have a sort to be cursor paginated");
                }

                return new CursorPaginator($results, $end['sort']);
            } else {
                return new CursorPaginator($results);
            }
        });

        // TODO: testing please
        $paginateByCursorMacro =
            function ($that, array $columns = []): CursorPaginator {
                /** @var Builder<Model> $that */
                $cursor = CursorPaginator::currentCursor();
                $cursorType = CursorPaginator::currentCursorType();

                $columns = collect($columns)
                    ->map(fn ($value, $key) => ['column' => $key, 'direction' => $value])
                    ->toArray();

                if (count($columns) == 0)
                    $columns = $that->getQuery()->orders;

                if (!is_array($columns) || count($columns) == 0)
                    throw new APIException('Cursor sort column count must be more than 0');

                if ($cursor) {
                    if (count($cursor) != count($columns))
                        throw new InvalidDataException('Invalid cursor used');

                    $apply = function ($query, $columns, $cursor, $cursorType) use (&$apply) {
                        $query->where(function ($query) use ($columns, $cursor, $cursorType, $apply) {
                            $column = $columns[key($columns)]['column'];
                            $direction = array_shift($columns)['direction'];
                            $value = array_shift($cursor);

                            $typeDirection1 = $cursorType == 'next' ? '>' : '<';
                            $typeDirection2 = $cursorType == 'next' ? '<' : '>';

                            $query->where($column, $direction === 'asc' ? $typeDirection1 : $typeDirection2, $value);

                            if (!empty($columns)) {
                                $query->orWhere($column, $value);
                                $apply($query, $columns, $cursor, $cursorType);
                            }
                        });
                    };

                    $apply($that, $columns, $cursor, $cursorType);
                }

                foreach ($columns as $data) {
                    if ($cursorType == 'previous') {
                        if ($data['direction'] == 'asc')
                            $data['direction'] = 'desc';
                        elseif ($data['direction'] == 'desc')
                            $data['direction'] = 'asc';
                    }
                    // phpstan thinks orders cant be nullable, it obviously was at some point or the ?? wouldnt have been fixed
                    // if only this was unit tested and i can just easily test if its still needed
                    // @phpstan-ignore-next-line
                    if (array_search($data, $that->getQuery()->orders ?? []) === false)
                        $that->orderBy($data['column'], $data['direction']);

                    if (is_a($data['column'], \Illuminate\Database\Query\Expression::class))
                        $that->select()->addSelect($data['column']);
                }

                $items = $that->limit(request()->get('limit') + 1)->get();

                if ($cursorType == 'previous')
                    $items = $items->reverse()->values();

                if ($items->count() == 0) {
                    return new CursorPaginator($items);
                }

                $columnNames = collect($columns)->map(fn ($value) => $value['column'])->toArray();

                if ($items->count() <= request()->get('limit')) {
                    if ($cursorType == 'previous') {
                        return new CursorPaginator($items, array_map(function ($column) use ($items) {
                            return $items->last()->getOriginal()[is_string($column) ? $column : $column->__toString()];
                        }, $columnNames));
                    } else {
                        return new CursorPaginator($items, [], array_map(function ($column) use ($items) {
                            return $items->first()->getOriginal()[is_string($column) ? $column : $column->__toString()];
                        }, $columnNames));
                    }
                }

                if ($cursorType == 'next')
                    $items->pop();
                else
                    $items->shift();

                $previousCursorMap = array_map(function ($column) use ($items) {
                    return $items->first()->getOriginal()[is_string($column) ? $column : $column->__toString()];
                }, $columnNames);

                return new CursorPaginator($items, array_map(function ($column) use ($items) {
                    return $items->last()->getOriginal()[is_string($column) ? $column : $column->__toString()];
                }, $columnNames), $previousCursorMap);
            };
        Builder::macro('paginateByCursor', fn (array $columns = []): CursorPaginator => $paginateByCursorMacro($this, $columns));
        Relation::macro('paginateByCursor', function (array $columns = []): CursorPaginator {
            /** @var Relation $this */
            return $this->getQuery()->paginateByCursor($columns);
        });

        Collection::macro('toKey', function ($key) {
            /** @var Collection $this */
            return $this->keyBy($key)->keys();
        });

        Collection::macro('remove', function ($key) {
            /** @var Collection $this */
            return $this->reject(function ($element) use ($key) {
                return $element == $key;
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
