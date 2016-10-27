<?php

namespace OpenClassrooms\Bundle\OneSkyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Romain Kuzniak <romain.kuzniak@openclassrooms.com>
 */
class OpenClassroomsOneSkyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');
        $config = $this->processConfiguration(new Configuration(), $configs);
        $this->processParameters($container, $config);
        $this->addThreadGateway($loader, $container);
    }

    private function processParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('openclassrooms_onesky.api_key', $config['api_key']);
        $container->setParameter('openclassrooms_onesky.api_secret', $config['api_secret']);
        $container->setParameter('openclassrooms_onesky.project_id', $config['project_id']);
        $container->setParameter('openclassrooms_onesky.source_locale', $config['source_locale']);
        $container->setParameter('openclassrooms_onesky.locales', $config['locales']);
        $container->setParameter('openclassrooms_onesky.file_format', $config['file_format']);
        $container->setParameter('openclassrooms_onesky.file_paths', $config['file_paths']);
        $container->setParameter('openclassrooms_onesky.keep_all_strings', $config['keep_all_strings']);
    }

    private function addThreadGateway(XmlFileLoader $loader, ContainerBuilder $container)
    {
        if (extension_loaded('pthread')) {
            $loader->load('services_pthread.xml');
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'openclassrooms_onesky';
    }
}
