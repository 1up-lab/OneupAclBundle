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
        $this->assertTrue(is_bool(self::$container->getParameter('oneup_acl.remove_orphans')));
    }

    public function testIfPermissionStrategyParameterIsSet()
    {
        $this->assertTrue(
            'any' == self::$container->getParameter('oneup_acl.permission_strategy') ||
            'all' == self::$container->getParameter('oneup_acl.permission_strategy') ||
            'equal' == self::$container->getParameter('oneup_acl.permission_strategy')
        );
    }
}
