<?php

namespace Oneup\AclBundle\Tests\Annotation;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Oneup\AclBundle\Annotation as Acl;

class DomainObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveAclProperty()
    {
        $annotation = new Acl\ObjectIdentity();
        $annotation->removeAcl = false;

        $this->assertFalse($annotation->removeAcl);
    }

    public function testIfAnnotationIsLoadable()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('Oneup\AclBundle\Annotation');

        $object = new \ReflectionClass('Oneup\AclBundle\Tests\Model\DomainObject');
        $annotations = $reader->getClassAnnotations($object);
        $objectIdentity = $annotations[0];

        $this->assertCount(1, $annotations);
        $this->assertInstanceOf('Oneup\AclBundle\Annotation\ObjectIdentity', $objectIdentity);
        $this->assertTrue($objectIdentity->removeAcl);
    }
}
