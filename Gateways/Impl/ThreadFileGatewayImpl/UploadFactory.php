<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl;

use Onesky\Api\Client;
use OpenClassrooms\Bundle\OneSkyBundle\Model\UploadFile;

/**
 * @author Kévin Letord <kevin.letord@openclassrooms.com>
 */
class UploadFactory
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LockableEventDispatcher
     */
    private $eventDispatcher;

    /**
     * @return Upload
     */
    public function createUpload(UploadFile $file)
    {
        return new Upload($this->eventDispatcher, clone $this->client, $file);
    }

    /**
     * @param Client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param LockableEventDispatcher
     */
    public function setEventDispatcher(LockableEventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
