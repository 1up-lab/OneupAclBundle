<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\DomainObject;

class AclManagerTest extends AbstractSecurityTest
{
    public function testAddOfObjectPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);
        $manager->addObjectPermission($object, $token, MaskBuilder::MASK_OWNER);

        $this->assertTrue($manager->isGranted('OWNER', $object));
        $this->assertTrue($manager->isGranted('VIEW', $object));
        $this->assertTrue($manager->isGranted('EDIT', $object));
        $this->assertFalse($manager->isGranted('NOT_EXISTANT', $object));

        $object = new DomainObject(2);

        $this->assertFalse($manager->isGranted('OWNER', $object));
        $this->assertFalse($manager->isGranted('VIEW', $object));
        $this->assertFalse($manager->isGranted('EDIT', $object));
        $this->assertFalse($manager->isGranted('NOT_EXISTANT', $object));
    }

    public function testSetOfObjectPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);

        $manager->addObjectPermission($object, $token, MaskBuilder::MASK_OWNER);
        $this->assertTrue($manager->isGranted('OWNER', $object));

        // overwrite
        $manager->setObjectPermission($object, $token, MaskBuilder::MASK_VIEW);
        $this->assertFalse($manager->isGranted('OWNER', $object));
        $this->assertTrue($manager->isGranted('VIEW', $object));
    }

    public function testRevokeOfObjectPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);

        $manager->addObjectPermission($object, $token, MaskBuilder::MASK_OWNER);
        $this->assertTrue($manager->isGranted('OWNER', $object));

        // revoke
        $manager->revokeObjectPermission($object, $token, MaskBuilder::MASK_OWNER);
        $this->assertFalse($manager->isGranted('OWNER', $object));
    }

    public function testAddOfClassPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);
        $manager->addClassPermission($object, $token, MaskBuilder::MASK_OWNER);

        $this->assertTrue($manager->isGranted('OWNER', $object));
        $this->assertTrue($manager->isGranted('VIEW', $object));
        $this->assertTrue($manager->isGranted('EDIT', $object));
        $this->assertFalse($manager->isGranted('NOT_EXISTANT', $object));

        $object = new DomainObject(2);

        $this->assertTrue($manager->isGranted('OWNER', $object));
        $this->assertTrue($manager->isGranted('VIEW', $object));
        $this->assertTrue($manager->isGranted('EDIT', $object));

        $this->assertFalse($manager->isGranted('NOT_EXISTANT', $object));
    }

    public function testSetOfClassPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object1 = new DomainObject(1);
        $object2 = new DomainObject(1);

        $manager->addObjectPermission($object1, $token, MaskBuilder::MASK_OWNER);
        $this->assertTrue($manager->isGranted('OWNER', $object1));
        $this->assertTrue($manager->isGranted('OWNER', $object2));

        // overwrite
        $manager->setObjectPermission($object1, $token, MaskBuilder::MASK_VIEW);
        $this->assertFalse($manager->isGranted('OWNER', $object1));
        $this->assertFalse($manager->isGranted('OWNER', $object2));
        $this->assertTrue($manager->isGranted('VIEW', $object1));
        $this->assertTrue($manager->isGranted('VIEW', $object2));
    }

    public function testRevokeOfClassPermission()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object1 = new DomainObject(1);
        $object2 = new DomainObject(2);

        $manager->addClassPermission($object1, $token, MaskBuilder::MASK_OWNER);
        $this->assertTrue($manager->isGranted('OWNER', $object1));
        $this->assertTrue($manager->isGranted('OWNER', $object2));

        // revoke
        $manager->revokeClassPermission($object1, $token, MaskBuilder::MASK_OWNER);
        $this->assertFalse($manager->isGranted('OWNER', $object1));
        $this->assertFalse($manager->isGranted('OWNER', $object2));
    }

    public function testRevokeOfObjectPermissions()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);

        $manager->addObjectPermission($object, $token, MaskBuilder::MASK_DELETE);
        $manager->addObjectPermission($object, $token, MaskBuilder::MASK_UNDELETE);
        $this->assertTrue($manager->isGranted('DELETE', $object));
        $this->assertTrue($manager->isGranted('UNDELETE', $object));

        // revoke
        $manager->revokeObjectPermissions($object, $token);
        $this->assertFalse($manager->isGranted('DELETE', $object));
        $this->assertFalse($manager->isGranted('UNDELETE', $object));
    }

    public function testRevokeOfClassPermissions()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object1 = new DomainObject(1);
        $object2 = new DomainObject(1);

        $manager->addClassPermission($object1, $token, MaskBuilder::MASK_DELETE);
        $manager->addClassPermission($object1, $token, MaskBuilder::MASK_UNDELETE);
        $this->assertTrue($manager->isGranted('DELETE', $object1));
        $this->assertTrue($manager->isGranted('UNDELETE', $object1));
        $this->assertTrue($manager->isGranted('DELETE', $object2));
        $this->assertTrue($manager->isGranted('UNDELETE', $object2));

        // revoke
        $manager->revokeClassPermissions($object1, $token);
        $this->assertFalse($manager->isGranted('DELETE', $object1));
        $this->assertFalse($manager->isGranted('UNDELETE', $object1));
        $this->assertFalse($manager->isGranted('DELETE', $object1));
        $this->assertFalse($manager->isGranted('UNDELETE', $object2));
    }

    public function testGrantPermissionObject()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);

        $manager->compile(
            $manager->grant($token)->access($object)->with(MaskBuilder::MASK_OWNER)
        );

        $this->assertTrue($manager->isGranted('OWNER', $object));
    }

    public function testRevokePermissionObject()
    {
        $manager = $this->getManager();
        $token = $this->getToken();

        $object = new DomainObject(1);

        $manager->compile(
            $manager->grant($token)->access($object)->with(MaskBuilder::MASK_OWNER)
        );

        $this->assertTrue($manager->isGranted('OWNER', $object));

        $manager->compile(
            $manager->revoke($token)->access($object)->with(MaskBuilder::MASK_OWNER)
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
