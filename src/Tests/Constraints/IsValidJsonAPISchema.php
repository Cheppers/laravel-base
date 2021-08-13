<?php

namespace Cheppers\LaravelBase\Tests\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use JsonSchema\Validator;

class IsValidJsonAPISchema extends Constraint
{

    public function matches($other):bool
    {
        $data = json_decode($other);
        $validator = new Validator();
        $validator->validate($data, (object)['$ref' => 'file://' . __DIR__ . '/JsonAPISchema.json']);
        return $validator->isValid();
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
