<?php
namespace ERD\RateableEntitiesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This automatically registers my form template with twig for use site wide
 */
class RegisterFormTemplatesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $templatesParam = 'twig.form.resources';

        if($container->hasParameter($templatesParam))
        {
            $templates = $container->getParameter($templatesParam);
            $templates[] = 'ERDRateableEntitiesBundle:Form:fields.html.twig';

            $container->setParameter($templatesParam, $templates);
        }
    }
}