<?php

namespace Cheppers\LaravelBase\Tests\Constraints;

use JsonSchema\Validator;
use PHPUnit\Framework\Constraint\Constraint;

class IsValidJsonSchema extends Constraint
{
    private $schema;

    /**
     * IsValidJsonSchema constructor.
     * @param object $schema json_decode object
     */
    public function __construct($schema)
    {
        parent::__construct();
        $this->schema = $schema;
    }

    /**
     * @param object $other json_decode object
     * @return mixed
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function matches($other):bool
    {
        $validator = new Validator();
        $validator->validate($other, $this->schema);
        if ($validator->isValid()) {
            return true;
        }
        foreach ($validator->getErrors() as $error) {
            throw new \PHPUnit_Framework_AssertionFailedError($error->getDataPath() . ': ' . $error->getMessage());
        }
        return false;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString():string
    {
        return 'is a valid schema';
    }
}
