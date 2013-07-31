<?php

namespace Oneup\AclBundle\Tests\Annotation;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Oneup\AclBundle\Annotation as Acl;

class DomainObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveAclProperty()
    {
        // this test is basically useless
        // but the annotation wont autoload if
        // we dont force it to do so.
        // dont blame the messenger
        $domainObject = new Acl\DomainObject();

        $classPermission = new Acl\ClassPermissions(array());
        $this->assertInstanceOf('Oneup\AclBundle\Annotation\DomainObject', $domainObject);
        $this->assertInstanceOf('Oneup\AclBundle\Annotation\ClassPermissions', $classPermission);
    }

    public function testIfAnnotationIsLoadable()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('Oneup\AclBundle\Annotation');

        $object = new \ReflectionClass('Oneup\AclBundle\Tests\Model\SomeObject');
        $annotations = $reader->getClassAnnotations($object);
        $objectIdentity = $annotations[0];

        $this->assertCount(1, $annotations);
        $this->assertInstanceOf('Oneup\AclBundle\Annotation\DomainObject', $objectIdentity);
    }
}
