<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl;

use Onesky\Api\Client;
use OpenClassrooms\Bundle\OneSkyBundle\EventListener\TranslationUploadTranslationEvent;
use OpenClassrooms\Bundle\OneSkyBundle\Model\UploadFile;

/**
 * @author Kévin Letord <kevin.letord@openclassrooms.com>
 */
class Upload extends \Thread
{
    /**
     * @var UploadFile
     */
    private $file;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LockableEventDispatcher
     */
    private $eventDispatcher;

    public function __construct(LockableEventDispatcher $eventDispatcher, Client $client, UploadFile $file)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->client = $client;
        $this->file = $file;
    }

    public function run()
    {
        $this->client->files('upload', $this->file->format());

        $this->eventDispatcher->dispatch(
            TranslationUploadTranslationEvent::getEventName(),
            new TranslationUploadTranslationEvent($this->file)
        );
    }
}
