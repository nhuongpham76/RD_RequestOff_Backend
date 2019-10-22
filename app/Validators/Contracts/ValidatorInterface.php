<?php

namespace App\Validators\Contracts;

use Illuminate\Contracts\Support\MessageBag;

/**
 * Interface ValidatorInterface
 *
 * @package Prettus\Validator\Contracts
 */
interface ValidatorInterface
{

    const RULE_CREATE = 'create';
    const RULE_UPDATE = 'update';

    /**
     * Set Id
     *
     * @param int $id Integer
     *
     * @return $this
     */
    public function setId($id);

    /**
     * With
     *
     * @param array $input Relation
     *
     * @return $this
     */
    public function with(array $input);

    /**
     * Pass the data and the rules to the validator
     *
     * @param string $action Action validator
     *
     * @return boolean
     */
    public function passes($action = null);

    /**
     * Pass the data and the rules to the validator or throws ValidatorException
     *
     * @param string $action Action validator
     *
     * @return boolean
     *
     * @throws ValidatorException
     */
    public function passesOrFail($action = null);

    /**
     * Errors
     *
     * @return array
     */
    public function errors();

    /**
     * Errors
     *
     * @return MessageBag
     */
    public function errorsBag();

    /**
     * Set Rules for Validation
     *
     * @param array       $rules  Rule validator
     * @param string|null $action Action
     *
     * @return $this
     */
    public function setRules(array $rules, $action = null);

    /**
     * Get rule for validation by action ValidatorInterface::RULE_CREATE or ValidatorInterface::RULE_UPDATE
     * Default rule: ValidatorInterface::RULE_CREATE
     *
     * @param string $action Action validator
     *
     * @return array
     */
    public function getRules($action = null);
}
