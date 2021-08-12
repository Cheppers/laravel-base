<?php

namespace Cheppers\LaravelBase\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Cheppers\LaravelBase\Exceptions\UnsuportedJsonRequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

abstract class ApiRequest extends FormRequest
{
    const NULLABLE = 'nullable';
    const STRING = 'string';
    const REQUIRED = 'required';
    const STRING_MAX = 'string|max:255';
    const REQUIRED_STRING = 'required|string';
    const REQUIRED_ARRAY = 'required|array';
    const REQUIRED_INTEGER = 'required|integer';
    const REQUIRED_STRING_MAX = 'required|string|max:255';
    const NULLABLE_STRING = 'nullable|string';
    const NULLABLE_STRING_MAX = 'nullable|string|max:255';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Add custom rules.
     *
     * @return array
     */
    protected function addRules(): array
    {
        return [];
    }

    /**
     * Defaine validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = ['data.type' => 'required|string'];
        return $rules + $this->addRules();
    }

    /**
     * Handle invalid json format.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws UnsuportedJsonRequestException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new UnsuportedJsonRequestException(
            $validator,
            $this->response(
                $validator->getMessageBag()->toArray()
            )
        );
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        if ($this->expectsJson()) {
            return $this->jsonResponse($errors);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }

    /**
     * Get the proper failed validation JSON response for the request.
     *
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponse(array $errors)
    {
        return new JsonResponse([
            'message' => 'The given data was invalid.',
            'errors' => $errors,
        ], 422);
    }
}
