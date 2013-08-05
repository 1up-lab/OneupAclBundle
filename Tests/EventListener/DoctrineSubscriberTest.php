<?php

namespace Oneup\AclBundle\Tests\EventListener;

use Oneup\AclBundle\EventListener\DoctrineSubscriber;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\SomeObject;

class DoctrineSubscriberTest extends AbstractSecurityTest
{
    protected $listener;

    public function setUp()
    {
        parent::setUp();

        $this->listener = new DoctrineSubscriber($this->container);
    }

    public function testPostPersistListener()
    {
        $object = new SomeObject(1);

        $this->assertFalse($this->manager->isGranted('VIEW', $object));
        $this->assertFalse($this->manager->isGranted('EDIT', $object));

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $args->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($object))
        ;

        $this->listener->postPersist($args);

        $this->assertTrue($this->manager->isGranted('VIEW', $object));
        $this->assertFalse($this->manager->isGranted('EDIT', $object));
    }
}
