<?php

namespace Oneup\AclBundle\Tests\DependencyInjection;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class OneupAclExtensionTest extends AbstractSecurityTest
{
    public function testIfTestSuiteLoads()
    {
        $this->assertTrue(true);
    }

    public function testIfOrphanRemovalParameterIsSet()
    {
        $this->assertTrue(is_bool($this->container->getParameter('oneup_acl.remove_orphans')));
    }

    public function testIfPermissionStrategyParameterIsSet()
    {
        $this->assertTrue(
            'any' == $this->container->getParameter('oneup_acl.permission_strategy') ||
            'all' == $this->container->getParameter('oneup_acl.permission_strategy') ||
            'equal' == $this->container->getParameter('oneup_acl.permission_strategy')
        );
    }
}
