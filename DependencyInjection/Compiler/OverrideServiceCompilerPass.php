<?php

namespace Oneup\AclBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('security.acl.dbal.provider');
        $definition->setClass('Oneup\AclBundle\Security\Authorization\Acl\AclProvider');
    }
}