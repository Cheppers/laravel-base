<?php

namespace Cheppers\LaravelBase\Tests\Constraints;

use League\JsonGuard\Validator;
use PHPUnit\Framework\Constraint\Constraint;

class IsValidJsonAPISchema extends Constraint
{

    public function matches($other):bool
    {
        $data = json_decode($other);
        $validator = new Validator($data, (object)['$ref' => 'file://' . __DIR__ . '/JsonAPISchema.json']);
        return $validator->passes();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString():string
    {
        return 'is a valid JsonApi response';
    }
}
