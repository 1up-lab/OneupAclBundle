<?php

namespace Oneup\AclBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Oneup\AclBundle\DependencyInjection\Compiler\MetaDataCompilerPass;

class OneupAclBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetaDataCompilerPass());
    }
}
