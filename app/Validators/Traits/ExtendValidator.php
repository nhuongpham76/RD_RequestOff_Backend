<?php

namespace App\Validators\Traits;

use App\Validators\Exceptions\ValidatorException;
use App\Validators\Contracts\ValidatorInterface;

trait ExtendValidator
{

    /**
     * Extend Validator
     *
     * @param array $data      Data
     * @param array $rules     Rule
     * @param array $mesages   Message
     * @param array $attribute Attribute
     *
     * @throws ValidatorException
     *
     * @return void
     */
    public function validateData($data, $rules, $mesages = [], $attribute = [])
    {
        $validator = $this->validator->make($data, $rules, $mesages, $attribute);
        if ($validator->fails()) {
            throw new ValidatorException($validator->messages(), ValidatorInterface::RULE_CREATE);
        }
    }
}
