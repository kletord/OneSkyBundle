<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl;

use Guzzle\Http\Exception\ServerErrorResponseException;
use Onesky\Api\Client;
use OpenClassrooms\Bundle\OneSkyBundle\EventListener\TranslationDownloadTranslationEvent;
use OpenClassrooms\Bundle\OneSkyBundle\EventListener\TranslationUploadTranslationEvent;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\InvalidContentException;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\NonExistingTranslationException;
use OpenClassrooms\Bundle\OneSkyBundle\Model\ExportFile;

/**
 * @author Kévin Letord <kevin.letord@openclassrooms.com>
 */
class Download extends \Thread
{
    /**
     * @var ExportFile
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

    public function __construct(LockableEventDispatcher $eventDispatcher, Client $client, ExportFile $file)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->client = $client;
        $this->file = $file;
    }

    public function run()
    {
        $fail = false;
        $downloadedContent = $this->client->translations('export', $this->file->format());

        try {
            $this->checkTranslation($downloadedContent, $this->file);
        } catch (NonExistingTranslationException $e) {
            $fail = true;
        }

        if (! $fail) {
            file_put_contents($this->file->getTargetFilePath(), $downloadedContent);
        }

        $this->eventDispatcher->dispatch(
            TranslationDownloadTranslationEvent::getEventName(),
            new TranslationDownloadTranslationEvent($this->file)
        );
    }

    /**
     * @throws InvalidContentException
     * @throws NonExistingTranslationException
     */
    private function checkTranslation($downloadedContent, ExportFile $file)
    {
        if (0 === strpos($downloadedContent, '{')) {
            $json = json_decode($downloadedContent, true);
            if (400 === $json['meta']['status']) {
                throw new NonExistingTranslationException($file->getTargetFilePath());
            }
            if (500 === $json['meta']['status']) {
                throw new ServerErrorResponseException($file->getTargetFilePath());
            }
            throw new InvalidContentException($downloadedContent);
        }
    }
}
