<?php

namespace App\Extensions\Search;

use OpenSearch\Client as OpenSearch;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class OpenSearchEngine extends Engine
{
    /**
     * The OpenSearch client.
     *
     * @var \OpenSearch\Client
     */
    protected $opensearch;

    /**
     * Determines if soft deletes for Scout are enabled or not.
     *
     * @var bool
     */
    protected $softDelete;

    /**
     * Create a new engine instance.
     *
     * @param  \OpenSearch\Client  $opensearch
     * @param  bool  $softDelete
     * @return void
     */
    public function __construct(OpenSearch $opensearch, $softDelete = false)
    {
        $this->opensearch = $opensearch;
        $this->softDelete = $softDelete;
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function update($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $index = $models->first()->searchableAs();

        if ($this->usesSoftDelete($models->first()) && $this->softDelete) {
            // @phpstan-ignore-next-line
            $models->each->pushSoftDeleteMetadata();
        }

        $objects = $models->map(function ($model) {
            if (empty($searchableData = $model->toSearchableArray())) {
                return;
            }

            return array_merge(
                [
                    'id' => $model->getScoutKey()
                ],
                $searchableData,
                $model->scoutMetadata(),
                [
                    '__morph_id' => $model->getMorphClass()
                ]
            );
        })->filter()->values()->all();

        if (!empty($objects)) {
            $params = ['body' => []];

            foreach ($objects as $key => $object) {
                /** @var int $key */
                $params['body'][] = [
                    'index' => [
                        '_index' => $index,
                        '_id'    => $object['id']
                    ]
                ];

                $params['body'][] = $object;

                if ($key % 1000 == 0) {
                    $this->opensearch->bulk($params);

                    $params = ['body' => []];
                }
            }

            if (!empty($params['body'])) {
                $this->opensearch->bulk($params);
            }
        }
    }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function delete($models)
    {
        $this->opensearch->deleteByQuery([
            'index' => $models->first()->searchableAs(),
            'body' => [
                'query' => [
                    'ids' => [
                        'values' => $models->map(function ($model) {
                            return $model->getScoutKey();
                        })->values()->all()
                    ]
                ]
            ]
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return $this->performSearch($builder, [
            'index' => $builder->index ?: $builder->model->searchableAs(),
            'body' => [
                '_source' => true,
                'size' => $builder->limit ? $builder->limit : 10,
                'from' => 0,
                'query' => $this->filters($builder)
            ]
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        return $this->performSearch($builder, [
            'index' => $builder->index ?: $builder->model->searchableAs(),
            'body' => [
                '_source' => true,
                'size' =>  $perPage ? $perPage : 10,
                'from' => ($page - 1) * $perPage,
                'query' => $this->filters($builder),
            ]
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  array  $cursor
     * @return mixed
     */
    public function cursorPaginate(Builder $builder, $perPage, $cursor)
    {
        $searchAfter = [];
        if (count($cursor) > 0) {
            $searchAfter = [
                'search_after' => $cursor
            ];
        }

        return $this->performSearch($builder, [
            'index' => $builder->index ?: $builder->model->searchableAs(),
            'body' => [
                '_source' => true,
                'size' =>  $perPage ? $perPage : 10,
                'query' => $this->filters($builder),
                ...$searchAfter
            ]
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  array  $options
     * @return mixed
     */
    protected function performSearch(Builder $builder, array $options = [])
    {
        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $this->opensearch,
                $builder->query,
                $options
            );
        }

        return $this->opensearch->search($options);
    }

    /**
     * Get the filter array for the query.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return array
     */
    protected function filters(Builder $builder)
    {
        if ($builder->query || count($builder->wheres) > 0) {
            $query = ['bool' => []];
        }

        if ($builder->query) {
            $query['bool'] = [
                'must' => [
                    [
                        'simple_query_string' => [
                            'query' => $builder->query,
                            'default_operator' => 'and',
                        ]
                    ]
                ]
            ];
        }

        if (count($builder->wheres) > 0) {
            foreach ($builder->wheres as $key => $value) {
                if ($key && $value) {
                    $query['bool']['filter'][] = [
                        'match' => [
                            $key => [
                                'query' => $value,
                                'operator' => 'and'
                            ]
                        ]
                    ];
                }
            }
        }

        return $query ?? [];
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        return collect($results['hits']['hits'])->pluck('_id')->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection<(int|string), mixed>
     */
    public function map(Builder $builder, $results, $model)
    {
        $hits = new HitsIteratorAggregate($results, $builder->queryCallback);

        return new Collection($hits);
    }

    /**
     * Map the given results to instances of the given model via a lazy collection.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\LazyCollection
     */
    public function lazyMap(Builder $builder, $results, $model)
    {
        if (count($results['hits']) === 0) {
            return LazyCollection::make($model->newCollection());
        }

        $objectIds = $this->mapIds($results)->all();
        $objectIdPositions = array_flip($objectIds);

        return $model->queryScoutModelsByIds(
            $builder,
            $objectIds
        )
            ->cursor()
            ->filter(function ($model) use ($objectIds) {
                return in_array($model->getScoutKey(), $objectIds);
            })
            ->sortBy(function ($model) use ($objectIdPositions) {
                return $objectIdPositions[$model->getScoutKey()];
            })
            ->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return (int) Arr::get($results, 'total.value', count($results['hits']));
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function flush($model)
    {
        $index = $model->searchableAs();
        $this->deleteIndex($index);
    }

    /**
     * Create a search index.
     *
     * @param  string  $name
     * @param  array  $options
     * @return mixed
     *
     * @throws \Exception
     */
    public function createIndex($name, array $options = [])
    {
        throw new \Exception('OpenSearch indexes are created automatically upon adding objects.');
    }

    /**
     * Delete a search index.
     *
     * @param  string  $name
     * @return mixed
     */
    public function deleteIndex($name)
    {
        return $this->opensearch->indices()->delete([
            'index' => $name
        ]);
    }

    /**
     * Determine if the given model uses soft deletes.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function usesSoftDelete($model)
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model));
    }

    /**
     * Dynamically call the OpenSearch client instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->opensearch->$method(...$parameters);
    }
}
