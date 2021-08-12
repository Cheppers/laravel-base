<?php

namespace Cheppers\LaravelBase\Transformers;

use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;

abstract class BaseResourceTransformer extends TransformerAbstract
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new Manager();
        $this->manager->setSerializer(new ArraySerializer());
    }

    protected function includeRelations($model, $baseData)
    {
        $includes = $this->getCurrentScope()
            ->getManager()
            ->getRequestedIncludes();
        $includeData = [];
        if (count($includes)) {
            foreach ($includes as $include) {
                $methodName = 'include' . ucfirst($include);
                if (method_exists($this, $methodName)) {
                    $includeData[$include] = $this->$methodName($model)
                        ->getData();
                }
            }
            $baseData['includes'] = $this->processIncludedResources(
                $this->getCurrentScope(),
                $model
            );
        }
        return $baseData;
    }
}
