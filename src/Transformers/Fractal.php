<?php

namespace Cheppers\LaravelBase\Transformers;

use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\Serializer\SerializerAbstract;

class Fractal
{
    protected $manager;

    public function __construct(SerializerAbstract $serializer)
    {
        $this->manager = new Manager();
        $this->manager->setSerializer($serializer);
    }

    public function parseIncludes($includes)
    {
        $this->manager->parseIncludes($includes);
    }

    public function collection($data, $transformer = null, $resourceKey = null)
    {
        $resource = new Collection($data, $this->getTransformer($transformer), $resourceKey);
        return $this->manager->createData($resource)->toArray();
    }


    public function item($data, $transformer = null, $resourceKey = null)
    {
        $resource = new Item($data, $this->getTransformer($transformer), $resourceKey);
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * Null response.
     */
    public function null()
    {
        $resource = new NullResource();
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * Pagination.
     *
     * @param \Illuminate\Support\Collection $paginator
     * @param \League\Fractal\TransformerAbstract $transformer
     */
    public function pagination($paginator, $transformer = null, $resourceKey = null)
    {
        $items = $paginator->getCollection();
        $resource = new Collection($items, $this->getTransformer($transformer), $resourceKey);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * @param \League\Fractal\TransformerAbstract $transformer
     * @return \League\Fractal\TransformerAbstract|callback
     */
    protected function getTransformer($transformer = null)
    {
        return $transformer ?: function ($data) {
            if ($data instanceof Arrayable) {
                return $data->toArray();
            }
            return (array) $data;
        };
    }
}
