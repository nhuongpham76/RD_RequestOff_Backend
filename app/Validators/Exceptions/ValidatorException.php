<?php

namespace App\Validators\Exceptions;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\MessageBag;

/**
 * Class ValidatorException
 *
 * @package Prettus\Validator\Exceptions
 */
class ValidatorException extends \RuntimeException implements Jsonable, Arrayable
{

    /**
     * MessageBag
     *
     * @var MessageBag
     */
    protected $messageBag;

    /**
     * String action
     *
     * @var string
     */
    protected $action;

    /**
     * Constructor.
     *
     * @param MessageBag $messageBag MessageBag
     * @param String     $action     Action
     *
     * @return void
     */
    public function __construct(MessageBag $messageBag, $action = null)
    {
        $this->messageBag = $messageBag;
        $this->action = $action;
    }

    /**
     * Get MessageBag.
     *
     * @return MessageBag
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->messageBag->getMessages();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options Options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
