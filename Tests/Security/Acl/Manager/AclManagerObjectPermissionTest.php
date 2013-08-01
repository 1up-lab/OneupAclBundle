<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\SomeObject;

class AclManagerObjectPermissionTest extends AbstractSecurityTest
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

    public function testAddOfObjectPermission()
    {
        // add permission to object1
        $this->manager->addObjectPermission($this->object1, $this->token, $this->mask1);

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
        // add permission to object1
        $this->manager->addObjectPermission($this->object1, $this->token, $this->mask1);

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
        // add permission to object1
        $this->manager->addObjectPermission($this->object1, $this->token, $this->mask1);

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
        // add permissions to object1
        $this->manager->addObjectPermission($this->object1, $this->token, $this->mask2);

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
        // add permissions to object1
        $this->manager->addObjectPermission($this->object1, $this->token, $this->mask1);
        $this->manager->addObjectPermission($this->object1, $this->token, $this->mask2);

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
