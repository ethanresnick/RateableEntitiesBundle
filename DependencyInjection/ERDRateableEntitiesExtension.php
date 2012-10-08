<?php

namespace ERD\RateableEntitiesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ERDRateableEntitiesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //prep config data & a service loader.
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));


        //if we're using doctrine, load its services
        if($config['use_doctrine_events'])
        {
            //sort of janky to do this here rather than in a compiler pass (at which point we could
            //inspect the kernel.bundles param), but to do it there would require making the service
            //below with a null class (or some other incompleteness we can detect), making a compiler
            //pass, and then checking if the service is incomplete in the pass (since we don't have 
            //easy access to the config to build the whole thing there) and doing our fill-in/exception logic.
            if (!class_exists('ERD\DoctrineHelpersBundle\ERDDoctrineHelpersBundle')) 
            {
                throw new \Exception("To have ERDRateableEntitiesBundle automatically operate on Doctrine's entities, you must first install the ERDDoctrineHelpersBundle.");
            }

            $loader->load('doctrine.xml');
        }

        $loader->load('form_types.xml');
    }    
}