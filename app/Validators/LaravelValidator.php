<?php
/**
 * Validator
 *
 * @author Phong Hunterist <phong@neo-lab.vn>
 */

namespace App\Validators;

use Illuminate\Validation\Factory;
use App\Validators\Contracts\AbstractValidator;

class LaravelValidator extends AbstractValidator
{

    /**
     * Validator
     *
     * @var \Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * Construct
     *
     * @param \Illuminate\Validation\Factory $validator Factory validator
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
//        app('db')->enableQueryLog();
        $validator = $this->validator->make($this->data, $rules, $messages, $this->getAttributes());
        if ($validator->fails()) {
//            dump(app('db')->getQueryLog());
            $this->errors = $validator->messages();
            return false;
        }
//        dump(app('db')->getQueryLog());
        return true;
    }
}
