<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl;

use OpenClassrooms\Bundle\OneSkyBundle\Gateways\FileGateway;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl\Download;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl\DownloadFactory;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl\Upload;
use OpenClassrooms\Bundle\OneSkyBundle\Gateways\Impl\ThreadFileGatewayImpl\UploadFactory;

/**
 * @author Kévin Letord <kevin.letord@openclassrooms.com>
 */
class ThreadFileGatewayImpl implements FileGateway
{
    /**
     * @var DownloadFactory
     */
    private $downloadFactory;

    /**
     * @var UploadFactory
     */
    private $uploadFactory;

    public function downloadTranslations(array $files)
    {
        $pool = new \Pool(8, Download::class);

        foreach ($files as $file) {
            $pool->submit($this->downloadFactory->createDownload($file));
        }

        $pool->shutdown();
    }

    public function uploadTranslations(array $files)
    {
        $pool = new \Pool(8, Upload::class);

        foreach ($files as $file) {
            $pool->submit($this->uploadFactory->createUpload($file));
        }

        $pool->shutdown();
    }

    /**
     * @param DownloadFactory
     */
    public function setDownloadFactory(DownloadFactory $downloadFactory)
    {
        $this->downloadFactory = $downloadFactory;
    }

    /**
     * @param UploadFactory
     */
    public function setUploadFactory(UploadFactory $uploadFactory)
    {
        $this->uploadFactory = $uploadFactory;
    }
}
