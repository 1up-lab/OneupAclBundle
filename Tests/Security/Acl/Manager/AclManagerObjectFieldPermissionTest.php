<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerObjectFieldPermissionTest extends AbstractSecurityTest
{
    public function testAddOfObjectFieldPermission()
    {
        // add permission to object1
        $this->manager->addObjectFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addObjectFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'bar'));

        // test object2
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object2, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object2, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object2, 'bar'));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object2, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object2, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2, 'bar'));
    }

    public function testSetOfObjectFieldPermission()
    {
        // add permission to object1
        $this->manager->addObjectFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addObjectFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'bar'));

        // overwrite
        $this->manager->setObjectFieldPermission($this->object1, 'foo', $this->mask2, $this->token);
        $this->manager->setObjectFieldPermission($this->object1, 'bar', $this->mask1, $this->token);

        // test the same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'bar'));
    }

    public function testRevokeOfObjectFieldPermission()
    {
        // add permission to object1
        $this->manager->addObjectFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addObjectFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'bar'));

        // revoke
        $this->manager->revokeObjectFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->revokeObjectFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test the same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'bar'));
    }

    public function testRevokeOfObjectFieldPermissions()
    {
        // add permission to object1
        $this->manager->addObjectFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addObjectFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'bar'));

        // revoke on field 'foo'
        $this->manager->revokeObjectFieldPermissions($this->object1, 'foo', $this->token);

        // test the same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
    }

    public function testRevokeOfAllObjectFieldPermissions()
    {
        // add permission to object1
        $this->manager->addObjectFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addObjectFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'bar'));

        // revoke all
        $this->manager->revokeAllObjectFieldPermissions($this->object1);

        // test the same object again
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
    }
}
