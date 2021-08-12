<?php

namespace Cheppers\LaravelBase\Http\Requests\Api;

class PatchRequest extends ApiRequest
{
    /**
     * Add custom rules.
     *
     * @return array
     */
    protected function addRules(): array
    {
        return [
            'data.id' => 'required',
            'data.attributes' => 'required',
        ];
    }
}
