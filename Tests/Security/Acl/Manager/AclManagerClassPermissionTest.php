<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\SomeObject;

class AclManagerClassPermissionTest extends AbstractSecurityTest
{
    protected $manager;
    protected $token;

    private $object1;
    private $object2;

    private $mask1;
    private $mask2;

    /**
     * basic setup
     */
    public function __construct()
    {
        $this->manager = $this->getManager();
        $this->token = $this->getToken();

        $this->object1 = new SomeObject(1);
        $this->object2 = new SomeObject(2);

        $builder1 = new MaskBuilder();
        $builder1
            ->add('view')
            ->add('create')
            ->add('edit')
        ;

        $this->mask1 = $builder1->get();

        $builder2 = new MaskBuilder();
        $builder2
            ->add('delete')
            ->add('undelete')
        ;

        $this->mask2 = $builder2->get();
    }

    public function testAddOfClassPermission()
    {
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask1);

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
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask1);

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
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask1);

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
        // add permission to class
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask1);
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask2);

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
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask1);
        $this->manager->addClassPermission($this->object1, $this->token, $this->mask2);

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
