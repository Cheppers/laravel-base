<?php

namespace Cheppers\LaravelBase\Repositories;

use App\Exceptions\Api\PagerLimitException;
use Cheppers\LaravelBase\Exceptions\InvalidFilteringException;
use Cheppers\LaravelBase\Exceptions\InvalidOrderingException;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository
{
    /**
     * @var $model \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $allowedOrders = [
        'id',
        'created_at',
    ];
    protected $allowedFilters;

    const PAGINATE_DEFAULT_LIMIT = 10;
    const PAGINATE_HARD_LIMIT = 1000;

    public function __construct()
    {
        $model = $this->getModelClass();
        $this->model = new $model();
    }

    abstract public function getModelClass();

    public function getPaginated($limit = null, $query = null)
    {
        if (empty($limit)) {
            $limit = static::PAGINATE_DEFAULT_LIMIT;
        }

        if ($limit > self::PAGINATE_HARD_LIMIT) {
            throw new PagerLimitException();
        }

        if (is_null($query)) {
            $query = $this->model->newQuery();
        }

        $paginated = $query->paginate($limit);
        $paginated->appends('limit', $limit);

        return $paginated;
    }

    public function getFilteredOrderedPaginated($filterInfo, $orderInfo, $paginationInfo)
    {
        $query = $this->model->newQuery();
        if (!is_null($filterInfo)) {
            $query = $this->getFiltered($filterInfo, $query);
        }
        if (!is_null($orderInfo)) {
            $query = $this->getOrdered($orderInfo, $query);
        }

        return $this->getPaginated($paginationInfo['limit'], $query);
    }

    public function getFilteredOrdered($filterInfo, $orderInfo)
    {
        $query = $this->model->newQuery();
        if (!is_null($filterInfo)) {
            $query = $this->getFiltered($filterInfo, $query);
        }
        if (!is_null($orderInfo)) {
            $query = $this->getOrdered($orderInfo, $query);
        }

        return $query->get();
    }

    protected function getOrdered($orderInfo, Builder $query)
    {
        if (is_null($orderInfo['order_by'])) {
            return $query;
        }
        if (is_null($orderInfo['sort_order'])) {
            $orderInfo['sort_order'] = 'asc';
        }

        if (!in_array($orderInfo['order_by'], $this->allowedOrders)
            || !in_array($orderInfo['sort_order'], ['asc', 'desc'])) {
            throw new InvalidOrderingException();
        }

        return $this->orderQuery($orderInfo, $query);
    }

    protected function orderQuery($orderInfo, Builder $query)
    {
        return $query->orderBy($orderInfo['order_by'], $orderInfo['sort_order']);
    }

    private function getFiltered($filterInfo, $query)
    {
        if (!is_array($filterInfo) || empty($filterInfo)) {
            throw new InvalidFilteringException('No filter info provided');
        }

        foreach ($filterInfo as $filterName => $filterValue) {
            if (is_null($filterValue)) {
                continue;
            }

            if (!in_array($filterName, $this->getAllowedFilters())) {
                throw new InvalidFilteringException('Filter not allowed');
            }
            $query = $this->filterQuery($filterName, $filterValue, $query);
        }

        return $query;
    }

    protected function filterQuery($filterName, $filterValue, $query)
    {
        switch ($filterName) {
            case 'title':
                return $query->where('title', 'LIKE', '%' . $filterValue . '%');
            default:
                return $query;
        }
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getNameById($id)
    {
        $item = $this->model->find($id);
        return $item->name;
    }

    public function getByIdOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        /**
         * @var $model \Illuminate\Database\Eloquent\Model
         */

        $model = $this->getByIdOrFail($id);
        $model->update($data);
        return $model;
    }

    public function getByAttribute($attribute, $data, $operator = '=')
    {
        return $this->model->where($attribute, $operator, $data)->first();
    }

    public function getAllowedOrders(): array
    {
        return $this->allowedOrders;
    }

    public function setAllowedOrders(array $allowedOrders)
    {
        $this->allowedOrders = $allowedOrders;
    }

    public function getAllowedFilters()
    {
        return $this->allowedFilters;
    }

    public function setAllowedFilters($allowedFilters)
    {
        $this->allowedFilters = $allowedFilters;
    }
}
