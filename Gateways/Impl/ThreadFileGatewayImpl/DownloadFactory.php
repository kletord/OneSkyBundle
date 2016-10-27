<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl;

use Onesky\Api\Client;
use OpenClassrooms\Bundle\OneSkyBundle\Model\ExportFile;

/**
 * @author Kévin Letord <kevin.letord@openclassrooms.com>
 */
class DownloadFactory
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
     * @return Download
     */
    public function createDownload(ExportFile $file)
    {
        return new Download($this->eventDispatcher, clone $this->client, $file);
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
