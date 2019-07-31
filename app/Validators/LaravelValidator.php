<?php

namespace App\Validators;

use Illuminate\Validation\Factory;
use App\Validators\Contracts\AbstractValidator;

class LaravelValidator extends AbstractValidator
{

    /**
     * Validator
     *
     * @var Factory
     */
    protected $validator;

    /**
     * Construct
     *
     * @param Factory $validator Factory validator
     *
     * @return void
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Pass the data and the rules to the validator
     *
     * @param string $action Action (create, update)
     *
     * @return bool
     */
    public function passes($action = null)
    {
        $rules = $this->getRules($action);
        $messages = $this->getMessages();

        $this->setAttributes($this->getAttributes());

        $validator = $this->validator->make($this->data, $rules, $messages, $this->getAttributes());

        if ($validator->fails()) {
            $this->errors = $validator->messages();
            return false;
        }

        return true;
    }
}
