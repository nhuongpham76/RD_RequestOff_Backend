<?php

namespace App\Validators\Contracts;

use Illuminate\Support\MessageBag;
use App\Exceptions\ValidatorException;

/**
 * Class AbstractValidator
 *
 * @package Prettus\Validator
 */
abstract class AbstractValidator implements ValidatorInterface
{

    /**
     * Id
     *
     * @var int
     */
    protected $id = null;

    /**
     * Validator
     *
     * @var object
     */
    protected $validator;

    /**
     * Data to be validated
     *
     * @var array
     */
    protected $data = array();

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Validation Custom Messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Validation Custom Attributes
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Validation errors
     *
     * @var MessageBag
     */
    protected $errors = array();

    /**
     * Set Id
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set data to validate
     *
     * @param array $data Data
     *
     * @return $this
     */
    public function with(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Return errors
     *
     * @return array
     */
    public function errors()
    {
        return $this->errorsBag()->all();
    }

    /**
     * Errors
     *
     * @return MessageBag
     */
    public function errorsBag()
    {
        return $this->errors;
    }

    /**
     * Pass the data and the rules to the validator
     *
     * @param string $action Action validator
     *
     * @return boolean
     */
    abstract public function passes($action = null);

    /**
     * Pass the data and the rules to the validator
     *
     * @param string $action Action validator
     *
     * @return boolean
     */
    public function passesOrFail($action = null)
    {
        if (!$this->passes($action)) {
            throw new ValidatorException($this->errorsBag(), $action);
        }
        return true;
    }

    /**
     * Get rule for validation by action ValidatorInterface::RULE_CREATE or ValidatorInterface::RULE_UPDATE
     * Default rule: ValidatorInterface::RULE_CREATE
     *
     * @param string $action Action validator
     *
     * @return array
     */
    public function getRules($action = null)
    {
        $rules = $this->rules;
        if (isset($this->rules[$action])) {
            $rules = $this->rules[$action];
        }
        return $this->parserValidationRules($rules, $this->id);
    }

    /**
     * Set Rules for Validation
     *
     * @param array       $rules  Rule
     * @param string|null $action Action
     *
     * @return $this
     */
    public function setRules(array $rules, $action = null)
    {
        if ($action) {
            $this->rules = array_merge($this->rules[$action], $rules);
        }
        $this->rules = array_merge($this->rules, $rules);
        return $this;
    }

    /**
     * Get Custom error messages for validation
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set Custom error
     *
     * @param array $messages messages for Validation
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Get Custom error attributes for validation
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set Custom error attributes for Validation
     *
     * @param array $attributes Attribute
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Parser Validation Rules
     *
     * @param array      $rules Rule validator
     * @param int|string $id    Id
     *
     * @return array
     */
    protected function parserValidationRules($rules, $id = null)
    {
        if ($id === null) {
            return $rules;
        }
        array_walk($rules, function (&$rules, $field) use ($id) {
            if (!is_array($rules)) {
                $rules = explode("|", $rules);
            }
            foreach ($rules as $ruleIdx => $rule) {
                // get name and parameters
                @list($name, $params) = array_pad(explode(":", $rule), 2, null);
                // only do something for the unique rule
                if (strtolower($name) != "unique") {
                    continue; // continue in foreach loop, nothing left to do here
                }
                $p = array_map("trim", explode(",", $params));
                // set field name to rules key ($field) (laravel convention)
                if (!isset($p[1])) {
                    $p[1] = $field;
                }
                // set 3rd parameter to id given to getValidationRules()
                $p[2] = $id;
                $params = implode(",", $p);
                $rules[$ruleIdx] = $name . ":" . $params;
            }
        });
        return $rules;
    }
}
