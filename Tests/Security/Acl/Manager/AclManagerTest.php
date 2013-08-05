<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerTest extends AbstractSecurityTest
{
    public function testIfPreloadFailsGracefullyIfNothingToLoad()
    {
        $ret = $this->manager->preload(array());
        $this->assertNull($ret);
    }

    public function testIfTokenMatchesIfNoneWasGiven()
    {
        $this->manager->addObjectPermission($this->object1, $this->mask1);
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
    }

    public function testIfTokenIsGrantedByGroup()
    {
        $adminToken = $this->createToken(array('ROLE_ADMIN'));

        $this->manager->addObjectPermission($this->object1, $this->mask1, 'ROLE_ADMIN');
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));

        // set token to admin token and try again
        $this->container->get('security.context')->setToken($adminToken);
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
    }

    public function testObjectGrantPermissionObject()
    {
        $this->manager->compile(
            $this->manager->grant($this->token)->accessTo($this->object1)->with($this->mask1)
        );

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
    }

    public function testObjectRevokePermissionObject()
    {
        $this->manager->compile(
            $this->manager->grant($this->token)->accessTo($this->object1)->with($this->mask1)
        );

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));

        $this->manager->compile(
            $this->manager->revoke($this->token)->accessTo($this->object1)->with($this->mask1)
        );

        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));
    }

    public function testIfAclManagerLoads()
    {
        $this->assertInstanceOf('Oneup\AclBundle\Security\Acl\Model\AclManagerInterface', $this->manager);
    }

    public function testIfAclManagerPropagatesIsGrantedCalls()
    {
        $this->assertTrue($this->manager->isGranted('ROLE_USER'));
        $this->assertFalse($this->manager->isGranted('ROLE_ADMIN'));
    }
}
