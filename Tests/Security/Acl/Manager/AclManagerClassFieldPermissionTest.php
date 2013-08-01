<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\SomeObject;

class AclManagerClassFieldPermissionTest extends AbstractSecurityTest
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

    public function testAddOfClassFieldPermission()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->token, $this->mask1);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->token, $this->mask2);

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
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->token, $this->mask1);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->token, $this->mask2);

        // test objects
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object2, 'bar'));

        // overwrite
        $this->manager->setClassFieldPermission($this->object1, 'foo', $this->token, $this->mask2);
        $this->manager->setClassFieldPermission($this->object2, 'bar', $this->token, $this->mask1);

        // test objects
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object2, 'foo'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'bar'));
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object2, 'bar'));
    }

    public function testRevokeOfClassFieldPermission()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->token, $this->mask1);

        // test object
        $this->assertTrue($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));

        // revoke
        $this->manager->revokeClassFieldPermission($this->object1, 'foo', $this->token, $this->mask1);

        // test object
        $this->assertFalse($this->manager->isGranted('VIEW', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('CREATE', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object1, 'foo'));
        $this->assertFalse($this->manager->isGranted('DELETE', $this->object1, 'foo'));
    }

    public function testRevokeOfClassFieldPermissions()
    {
        // add permission to class
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->token, $this->mask1);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->token, $this->mask2);

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
        $this->manager->addClassFieldPermission($this->object1, 'foo', $this->token, $this->mask1);
        $this->manager->addClassFieldPermission($this->object1, 'bar', $this->token, $this->mask2);

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
