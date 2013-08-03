<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclManagerClassPermissionTest extends AbstractSecurityTest
{
    public function testAddOfClassPermission()
    {
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->mask1, $this->token);

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
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->mask1, $this->token);

        // test objects
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2));

        // overwrite
        $this->manager->setClassPermission($this->object1, $this->mask2, $this->token);

        // test objects
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object2));
    }

    public function testRevokeOfClassPermission()
    {
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->mask1, $this->token);

        // test objects
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2));

        // revoke
        $this->manager->revokeClassPermission($this->object1, $this->mask1, $this->token);

        // test objects
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2));
    }

    public function testRevokeOfClassPermissions()
    {
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->mask1, $this->token);
        $this->manager->addClassPermission($this->object1, $this->mask2, $this->token);

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
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->mask1, $this->token);
        $this->manager->addClassPermission($this->object1, $this->mask2, $this->token);

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
