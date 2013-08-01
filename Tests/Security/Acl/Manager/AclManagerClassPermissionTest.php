<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerClassPermissionTest extends AbstractSecurityTest
{
    public function testGrantOfClassPermission()
    {
        // grant permission to class
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask1);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1));

        // test object2
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object2));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object2));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object2));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object2));
    }

    public function testSetOfClassPermission()
    {
        // grant permission to class
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask1);

        // test objects
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2));

        // overwrite
        $this->manager->setClassPermission($this->object1, $this->token, $this->mask2);

        // test objects
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object2));
    }

    public function testRevokeOfClassPermission()
    {
        // grant permission to class
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask1);

        // test objects
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2));

        // revoke
        $this->manager->revokeClassPermission($this->object1, $this->token, $this->mask1);

        // test objects
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2));
    }

    public function testRevokeOfClassPermissions()
    {
        // grant permission to class
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask1);
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask2);

        // test object
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1));

        // revoke
        $this->manager->revokeClassPermissions($this->object1, $this->token);

        // test same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1));
    }

    public function testRevokeOfAllClassPermissions()
    {
        // grant permission to class
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask1);
        $this->manager->grantClassPermission($this->object1, $this->token, $this->mask2);

        // test object
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1));

        // revoke
        $this->manager->revokeAllClassPermissions($this->object1);

        // test same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1));
    }
}
