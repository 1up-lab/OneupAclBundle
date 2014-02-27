<?php

namespace Oneup\AclBundle\Tests\EventListener;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\SomeObject;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class DoctrineSubscriberTest extends AbstractSecurityTest
{
    protected $listener;

    public function setUp()
    {
        parent::setUp();

        $this->listener = $this->container->get('oneup_acl.doctrine_subscriber');
    }

    public function testPostPersistListener()
    {
        $this->markTestIncomplete();

        // Test with Doctrine\Common\Persistence\Event\LifecycleEventArgs
        $object = new SomeObject(1);

        $this->assertFalse($this->manager->isGranted('VIEW', $object));
        $this->assertFalse($this->manager->isGranted('EDIT', $object));

        $args = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $args->expects($this->any())
            ->method('getObject')
            ->will($this->returnValue($object))
        ;

        $this->listener->postPersist($args);

        $this->assertTrue($this->manager->isGranted('VIEW', $object));
        $this->assertFalse($this->manager->isGranted('EDIT', $object));

        // Test with Doctrine\ORM\Event\LifecycleEventArgs
        $object = new SomeObject(2);

        $this->assertFalse($this->manager->isGranted('VIEW', $object));
        $this->assertFalse($this->manager->isGranted('EDIT', $object));

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $args->expects($this->any())
            ->method('getObject')
            ->will($this->returnValue($object))
        ;

        $this->listener->postPersist($args);

        $this->assertTrue($this->manager->isGranted('VIEW', $object));
        $this->assertFalse($this->manager->isGranted('EDIT', $object));
    }

    public function testPreRemoveListener()
    {
        $object = new SomeObject(1);

        // Test with Doctrine\Common\Persistence\Event\LifecycleEventArgs
        $this->assertFalse($this->manager->isGranted('OWNER', $object));
        $this->assertFalse($this->manager->isGranted('VIEW', $object, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $object, 'bar'));

        $this->manager->addObjectPermission($object, MaskBuilder::MASK_OWNER);
        $this->manager->addObjectFieldPermission($object, 'foo', MaskBuilder::MASK_VIEW);
        $this->manager->addObjectFieldPermission($object, 'bar', MaskBuilder::MASK_EDIT);

        $this->assertTrue($this->manager->isGranted('OWNER', $object));
        $this->assertTrue($this->manager->isGranted('VIEW', $object, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $object, 'bar'));

        $args = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $args->expects($this->any())
            ->method('getObject')
            ->will($this->returnValue($object))
        ;

        $this->listener->preRemove($args);

        $this->assertFalse($this->manager->isGranted('OWNER', $object));
        $this->assertFalse($this->manager->isGranted('VIEW', $object, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $object, 'bar'));

        // Test with Doctrine\ORM\Event\LifecycleEventArgs
        $object = new SomeObject(2);

        $this->assertFalse($this->manager->isGranted('OWNER', $object));
        $this->assertFalse($this->manager->isGranted('VIEW', $object, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $object, 'bar'));

        $this->manager->addObjectPermission($object, MaskBuilder::MASK_OWNER);
        $this->manager->addObjectFieldPermission($object, 'foo', MaskBuilder::MASK_VIEW);
        $this->manager->addObjectFieldPermission($object, 'bar', MaskBuilder::MASK_EDIT);

        $this->assertTrue($this->manager->isGranted('OWNER', $object));
        $this->assertTrue($this->manager->isGranted('VIEW', $object, 'foo'));
        $this->assertTrue($this->manager->isGranted('EDIT', $object, 'bar'));

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $args->expects($this->any())
            ->method('getObject')
            ->will($this->returnValue($object))
        ;

        $this->listener->preRemove($args);

        $this->assertFalse($this->manager->isGranted('OWNER', $object));
        $this->assertFalse($this->manager->isGranted('VIEW', $object, 'foo'));
        $this->assertFalse($this->manager->isGranted('EDIT', $object, 'bar'));
    }
}
