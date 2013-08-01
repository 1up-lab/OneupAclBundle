<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerObjectPermissionTest extends AbstractSecurityTest
{
    public function testGrantOfObjectPermission()
    {
        // grant permission to object1
        $this->manager->grantObjectPermission($this->object1, $this->token, $this->mask1);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1));
        $this->assertFalse($this->manager->isGranted('NOT_EXISTENT', $this->object1));

        // test object2
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object2));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object2));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object2));
        $this->assertFalse($this->manager->isGranted('NOT_EXISTENT', $this->object2));
    }

    public function testSetOfObjectPermission()
    {
        // grant permission to object1
        $this->manager->grantObjectPermission($this->object1, $this->token, $this->mask1);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));

        // overwrite permissions
        $this->manager->setObjectPermission($this->object1, $this->token, $this->mask2);

        // test same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1));
    }

    public function testRevokeOfObjectPermission()
    {
        // grant permission to object1
        $this->manager->grantObjectPermission($this->object1, $this->token, $this->mask1);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));

        // revoke
        $this->manager->revokeObjectPermission($this->object1, $this->token, $this->mask1);

        // test same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));
    }

    public function testRevokeOfObjectPermissions()
    {
        // grant permissions to object1
        $this->manager->grantObjectPermission($this->object1, $this->token, $this->mask2);

        // test object1
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1));

        // revoke
        $this->manager->revokeObjectPermissions($this->object1, $this->token);

        // test same object again
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1));
    }

    public function testRevokeOfAllObjectPermissions()
    {
        // grant permissions to object1
        $this->manager->grantObjectPermission($this->object1, $this->token, $this->mask1);
        $this->manager->grantObjectPermission($this->object1, $this->token, $this->mask2);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1));

        // revoke
        $this->manager->revokeAllObjectPermissions($this->object1);

        // test the same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1));

    }
}
