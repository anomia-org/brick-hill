<?php

namespace App\Extensions\Search;

use Illuminate\Database\Eloquent\Relations\Relation;
use IteratorAggregate;
use Laravel\Scout\Builder;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

use Traversable;

/**
 * @internal
 */
final class HitsIteratorAggregate implements IteratorAggregate
{
    /**
     * @var array
     */
    private $results;

    /**
     * @var callable|null
     */
    private $callback;

    /**
     * @param  array  $results
     * @param  callable|null  $callback
     */
    public function __construct(array $results, callable $callback = null)
    {
        $this->results = $results;
        $this->callback = $callback;
    }

    /**
     * Retrieve an external iterator.
     *
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *                     <b>Traversable</b>
     *
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        $hits = collect();
        if ($this->results['hits']['total']) {
            $hits = $this->results['hits']['hits'];
            $models = collect($hits)->groupBy('_source.__morph_id')
                ->map(function ($results, $morphId) {
                    // this type is accepted by intelephense but phpstan doesnt like it ig there is no actual way to define it in php
                    // @phpstan-ignore-next-line
                    /** @var Model&Searchable $model */
                    $model = new (Relation::getMorphedModel($morphId));
                    $model->setKeyType('string');
                    $builder = new Builder($model, '');
                    if (!empty($this->callback)) {
                        $builder->query($this->callback);
                    }

                    return $models = $model->getScoutModelsByIds(
                        $builder,
                        $results->pluck('_id')->all()
                    );
                })
                ->flatten()->keyBy(function ($model) {
                    return $model->getMorphClass() . '::' . $model->getScoutKey();
                });
            $hits = collect($hits)->map(function ($hit) use ($models) {
                $key = $hit['_source']['__morph_id'] . '::' . $hit['_id'];

                return isset($models[$key]) ? $models[$key] : null;
            })->filter()->all();
        }

        return new \ArrayIterator((array) $hits);
    }
}
