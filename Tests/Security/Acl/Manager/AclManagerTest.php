<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\SomeObject;

class AclManagerTest extends AbstractSecurityTest
{
    public function testIfTokenIsGrantedByGroup()
    {
        $adminToken = $this->createToken(array('ROLE_ADMIN'));
        $manager = $this->getManager();

        $object = new SomeObject(1);

        $manager->addObjectPermission($object, 'ROLE_ADMIN', MaskBuilder::MASK_VIEW);
        $this->assertFalse($manager->isGranted('VIEW', $object));

        // set token to admin token and try again
        $this->container->get('security.context')->setToken($adminToken);
        $this->assertTrue($manager->isGranted('VIEW', $object));
    }

    public function testObjectGrantPermissionObject()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new SomeObject(1);

        $manager->compile(
            $manager->grant($token)->accessTo($object)->with(MaskBuilder::MASK_OWNER)
        );

        $this->assertTrue($manager->isGranted('OWNER', $object));
    }

    public function testObjectRevokePermissionObject()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new SomeObject(1);

        $manager->compile(
            $manager->grant($token)->accessTo($object)->with(MaskBuilder::MASK_OWNER)
        );

        $this->assertTrue($manager->isGranted('OWNER', $object));

        $manager->compile(
            $manager->revoke($token)->accessTo($object)->with(MaskBuilder::MASK_OWNER)
        );

        $this->assertFalse($manager->isGranted('OWNER', $object));
    }

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
