<?php
namespace ERD\RateableEntitiesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ERD\RateableEntitiesBundle\DependencyInjection\Compiler\RegisterFormTemplatesCompilerPass;

class ERDRateableEntitiesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterFormTemplatesCompilerPass());
    }
}