<?php

namespace App\Repositories\Traits;

use App\Exceptions\ValidatorException;

trait ExtendValidator
{

    /**
     * Extend Validator
     *
     * @param array $data      Data
     * @param array $rules     Rule
     * @param array $messages  Message
     * @param array $attribute Attribute
     *
     * @throws ValidatorException
     *
     * @return void
     */
    public function validateData($data, $rules = null, $messages = [], $attribute = [])
    {
        $rules = $rules ?? $this->rules;

        $validator = $this->validator->make($data, $rules, $messages, $attribute);

        if ($validator->fails()) {
            throw new ValidatorException($validator->messages());
        }
    }
}
