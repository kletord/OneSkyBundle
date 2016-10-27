<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Kévin Letord <kevin.letord@openclassrooms.com>
 */
class LockableEventDispatcher extends EventDispatcher
{
    /**
     * @var \Mutex
     */
    private $mutex;

    public function __construct()
    {
        $this->mutex = \Mutex::create();
    }

    public function dispatch($eventName, Event $event = null)
    {
        \Mutex::lock($this->mutex);
        $result = parent::dispatch($eventName, $event);
        \Mutex::unlock($this->mutex);

        return $result;
    }

}
