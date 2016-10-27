<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl;

use Guzzle\Http\Exception\ServerErrorResponseException;
use Onesky\Api\Client;
use OpenClassrooms\Bundle\OneSkyBundle\EventListener\TranslationDownloadTranslationEvent;
use OpenClassrooms\Bundle\OneSkyBundle\EventListener\TranslationUploadTranslationEvent;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\FileGateway;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\InvalidContentException;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\NonExistingTranslationException;
use OpenClassrooms\Bundle\OneSkyBundle\Model\ExportFile;
use OpenClassrooms\Bundle\OneSkyBundle\Model\UploadFile;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Romain Kuzniak <romain.kuzniak@openclassrooms.com>
 */
class FileGatewayImpl implements FileGateway
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    public function downloadTranslations(array $files)
    {
        $downloadedFiles = [];
        foreach ($files as $file) {
            try {
                $downloadedFiles[] = $this->downloadTranslation($file);
            } catch (NonExistingTranslationException $ne) {
            }
        }

        return $downloadedFiles;
    }

    /**
     * @return ExportFile
     *
     * @throws InvalidContentException
     * @throws NonExistingTranslationException
     */
    private function downloadTranslation(ExportFile $file)
    {
        $downloadedContent = $this->client->translations(self::DOWNLOAD_METHOD, $file->format());
        $this->checkTranslation($downloadedContent, $file);
        file_put_contents($file->getTargetFilePath(), $downloadedContent);

        $this->eventDispatcher->dispatch(
            TranslationDownloadTranslationEvent::getEventName(),
            new TranslationDownloadTranslationEvent($file)
        );
        return $file;
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

    /**
     * {@inheritdoc}
     */
    public function uploadTranslations(array $files)
    {
        $uploadedFiles = [];
        foreach ($files as $file) {
            $uploadedFiles[] = $this->uploadTranslation($file);
        }

        return $uploadedFiles;
    }

    /**
     * @return UploadFile
     */
    private function uploadTranslation(UploadFile $file)
    {
        $this->client->files(self::UPLOAD_METHOD, $file->format());

        $this->eventDispatcher->dispatch(
            TranslationUploadTranslationEvent::getEventName(),
            new TranslationUploadTranslationEvent($file)
        );
        return $file;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
