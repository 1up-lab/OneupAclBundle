<?php

namespace Oneup\AclBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;

class OneupAclExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('security.xml');
        $loader->load('driver.xml');
        $loader->load('doctrine.xml');

        if (class_exists('Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle')) {
            $loader->load('configuration.xml');
        }

        $strategy = constant(
            sprintf(
                'Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy::%s',
                strtoupper($config['permission_strategy'])
            )
        );

        $container->setParameter('oneup_acl.remove_orphans', $config['remove_orphans']);
        $container->setParameter('oneup_acl.permission_strategy', $strategy);
    }
}
