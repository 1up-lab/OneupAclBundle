<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerTest extends AbstractSecurityTest
{
    public function testIfAclManagerLoads()
    {
        $manager = $this->getManager();

        $this->assertInstanceOf('Oneup\AclBundle\Security\Acl\Model\AclManagerInterface', $manager);
    }

    public function testIfAclManagerPropagatesIsGrantedCalls()
    {
        $manager = $this->getManager();

        $this->assertTrue($manager->isGranted('ROLE_USER'));
        $this->assertFalse($manager->isGranted('ROLE_ADMIN'));
    }
}
