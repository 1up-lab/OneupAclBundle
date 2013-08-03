<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerClassFieldPermissionTest extends AbstractSecurityTest
{
    public function testAddOfClassFieldPermission()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object1
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('NOT_EXISTENT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('NOT_EXISTENT', $this->object1, 'bar'));

        // test object2
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object2, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object2, 'bar'));
        $this->assertFalse($this->manager->isGranted('NOT_EXISTENT', $this->object2, 'foo'));
        $this->assertFalse($this->manager->isGranted('NOT_EXISTENT', $this->object2, 'bar'));
    }

    public function testSetOfClassFieldPermission()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test objects
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object2, 'bar'));

        // overwrite
        $this->manager->setClassFieldPermission($this->object1, 'foo', $this->mask2, $this->token);
        $this->manager->setClassFieldPermission($this->object2, 'bar', $this->mask1, $this->token);

        // test objects
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2, 'bar'));
    }

    public function testRevokeOfClassFieldPermission()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->mask1, $this->token);

        // test object
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));

        // revoke
        $this->manager->revokeClassFieldPermission($this->object1, 'foo', $this->mask1, $this->token);

        // test object
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
    }

    public function testRevokeOfClassFieldPermissions()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));

        // revoke
        $this->manager->revokeClassFieldPermissions($this->object1, 'foo', $this->token);

        // test object
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
    }

    public function testRevokeOfAllClassFieldPermissions()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->mask1, $this->token);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->mask2, $this->token);

        // test object
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));

        // revoke
        $this->manager->revokeAllClassFieldPermissions($this->object1);

        // test object
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertFalse($this->manager->isGranted('UNDELETE', $this->object1, 'bar'));
    }
}
