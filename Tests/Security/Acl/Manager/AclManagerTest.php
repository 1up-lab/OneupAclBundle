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

    public function testAddOfClassFieldPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new SomeObject(1);

        $manager->addClassFieldPermission($object, 'foo', $token, MaskBuilder::MASK_OWNER);
        $manager->addClassFieldPermission($object, 'bar', $token, MaskBuilder::MASK_VIEW);

        $this->assertTrue($manager->isGranted('OWNER', $object, 'foo'));
        $this->assertTrue($manager->isGranted('EDIT', $object, 'foo'));
        $this->assertTrue($manager->isGranted('VIEW', $object, 'bar'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object, 'foo'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object, 'bar'));

        $object = new SomeObject(2);

        $this->assertTrue($manager->isGranted('OWNER', $object, 'foo'));
        $this->assertTrue($manager->isGranted('VIEW', $object, 'bar'));
        $this->assertTrue($manager->isGranted('EDIT', $object, 'foo'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object, 'foo'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object, 'bar'));
    }

    public function testSetOfClassFieldPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object1 = new SomeObject(1);
        $object2 = new SomeObject(2);

        $manager->addClassFieldPermission($object1, 'foo', $token, MaskBuilder::MASK_OWNER);
        $manager->addClassFieldPermission($object2, 'bar', $token, MaskBuilder::MASK_VIEW);
        $this->assertTrue($manager->isGranted('OWNER', $object1, 'foo'));
        $this->assertTrue($manager->isGranted('OWNER', $object2, 'foo'));
        $this->assertTrue($manager->isGranted('VIEW', $object1, 'bar'));
        $this->assertTrue($manager->isGranted('VIEW', $object2, 'bar'));

        // overwrite
        $manager->setClassFieldPermission($object1, 'foo', $token, MaskBuilder::MASK_VIEW);
        $manager->setClassFieldPermission($object2, 'bar', $token, MaskBuilder::MASK_OWNER);
        $this->assertFalse($manager->isGranted('OWNER', $object1, 'foo'));
        $this->assertFalse($manager->isGranted('OWNER', $object2, 'foo'));
        $this->assertTrue($manager->isGranted('VIEW', $object1, 'bar'));
        $this->assertTrue($manager->isGranted('VIEW', $object2, 'bar'));
    }

    public function testRevokeOfClassFieldPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object1 = new SomeObject(1);
        $object2 = new SomeObject(2);

        $manager->addClassFieldPermission($object1, 'foo', $token, MaskBuilder::MASK_OWNER);
        $manager->addClassFieldPermission($object2, 'bar', $token, MaskBuilder::MASK_VIEW);
        $this->assertTrue($manager->isGranted('OWNER', $object1, 'foo'));
        $this->assertTrue($manager->isGranted('OWNER', $object2, 'foo'));
        $this->assertTrue($manager->isGranted('VIEW', $object1, 'bar'));
        $this->assertTrue($manager->isGranted('VIEW', $object2, 'bar'));

        // revoke
        $manager->revokeClassFieldPermission($object1, 'foo', $token, MaskBuilder::MASK_OWNER);
        $manager->revokeClassFieldPermission($object2, 'bar', $token, MaskBuilder::MASK_VIEW);
        $this->assertFalse($manager->isGranted('OWNER', $object1, 'foo'));
        $this->assertFalse($manager->isGranted('OWNER', $object2, 'foo'));
        $this->assertFalse($manager->isGranted('VIEW', $object1, 'bar'));
        $this->assertFalse($manager->isGranted('VIEW', $object2, 'bar'));
    }

    public function testRevokeOfAllClassFieldPermissions()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object1 = new SomeObject(1);
        $object2 = new SomeObject(2);

        $manager->addClassFieldPermission($object1, 'foo', $token, MaskBuilder::MASK_OWNER);
        $manager->addClassFieldPermission($object2, 'bar', $token, MaskBuilder::MASK_VIEW);

        $this->assertTrue($manager->isGranted('OWNER', $object1, 'foo'));
        $this->assertTrue($manager->isGranted('EDIT', $object2, 'foo'));
        $this->assertTrue($manager->isGranted('VIEW', $object1, 'bar'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object1, 'foo'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object2, 'bar'));

        $manager->revokeAllClassFieldPermissions($object1);

        $this->assertFalse($manager->isGranted('OWNER', $object1, 'foo'));
        $this->assertFalse($manager->isGranted('VIEW', $object2, 'bar'));
        $this->assertFalse($manager->isGranted('EDIT', $object1, 'foo'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object1, 'foo'));
        $this->assertFalse($manager->isGranted('NOT_EXISTENT', $object2, 'bar'));
    }
}
