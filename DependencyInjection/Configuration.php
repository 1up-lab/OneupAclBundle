<?php

namespace Oneup\AclBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oneup_acl');

        $rootNode
            ->children()
                ->booleanNode('remove_orphans')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
