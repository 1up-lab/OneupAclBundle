<?php

namespace Oneup\AclBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Oneup\AclBundle\Configuration\ParamPermission;
use Oneup\AclBundle\EventListener\PermissionListener;
use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;
use Oneup\AclBundle\Tests\Model\TestController;
use Oneup\AclBundle\Tests\Model\SomeObject;

class PermissionListenerTest extends AbstractSecurityTest
{
    protected $listener;

    public function setUp()
    {
        parent::setUp();

        $manager = $this->getManager();
        $listener = new PermissionListener($manager);

        $this->listener = $listener;
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAccessDenied()
    {
        $object = new SomeObject(1);

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getController')
            ->will($this->returnValue(array(
                new TestController,
                'oneAction'
            )))
        ;

        $checks = array(
            new ParamPermission(array('value' => array('one' => 128)))
        );

        $request = new Request(array(), array(), array(
            '_acl_permission' => $checks,
            'one' => $object
        ));

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $this->listener->onKernelController($event);
    }

    public function testAccessGranted()
    {
        $object = new SomeObject(1);
        $this->manager->addObjectPermission($object, MaskBuilder::MASK_VIEW, $this->getToken());

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getController')
            ->will($this->returnValue(array(
                new TestController,
                'oneAction'
            )))
        ;

        $checks = array(
            new ParamPermission(array('value' => array('one' => 'VIEW')))
        );

        $request = new Request(array(), array(), array(
            '_acl_permission' => $checks,
            'one' => $object
        ));

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $this->listener->onKernelController($event);
    }

    public function testMultiplePermissionInSingleAnnotation()
    {
        $object1 = new SomeObject(1);
        $object2 = new SomeObject(2);
        $this->manager->addObjectPermission($object1, MaskBuilder::MASK_VIEW, $this->getToken());
        $this->manager->addObjectPermission($object2, MaskBuilder::MASK_VIEW, $this->getToken());

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getController')
            ->will($this->returnValue(array(
                new TestController,
                'twoAction'
            )))
        ;

        $checks = array(
            new ParamPermission(array('value' => array(
                'one' => 'VIEW',
                'two' => 'VIEW'
            )))
        );

        $request = new Request(array(), array(), array(
            '_acl_permission' => $checks,
            'one' => $object1,
            'two' => $object2
        ));

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $this->listener->onKernelController($event);
    }

    public function testMultiplePermissionsInAnnotation()
    {
        $object1 = new SomeObject(1);
        $object2 = new SomeObject(2);
        $this->manager->addObjectPermission($object1, MaskBuilder::MASK_VIEW, $this->getToken());
        $this->manager->addObjectPermission($object2, MaskBuilder::MASK_VIEW, $this->getToken());

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getController')
            ->will($this->returnValue(array(
                new TestController,
                'threeAction'
            )))
        ;

        $checks = array(
            new ParamPermission(array('value' => array('one' => 'VIEW'))),
            new ParamPermission(array('value' => array('two' => 'VIEW')))
        );

        $request = new Request(array(), array(), array(
            '_acl_permission' => $checks,
            'one' => $object1,
            'two' => $object2
        ));

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $this->listener->onKernelController($event);
    }
}
