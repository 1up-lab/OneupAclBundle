<?php

namespace Oneup\AclBundle\Tests\Annotation;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Oneup\AclBundle\Mapping\Annotation as Acl;

class DomainObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testAutoloadDummy()
    {
        // this test is basically useless
        // but the annotation wont autoload if
        // we dont force it to do so.
        // dont blame the messenger
        $domainObject = new Acl\DomainObject();
        $classPermission = new Acl\ClassPermission(array());
        $propertyPermission = new Acl\PropertyPermission(array());

        $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\DomainObject', $domainObject);
        $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\ClassPermission', $classPermission);
        $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\PropertyPermission', $propertyPermission);
    }

    public function testIfAnnotationIsLoadable()
    {
        $annotations = $this->getDomainObjectAnnotations();

        $this->assertCount(1, $annotations);
        $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\DomainObject', $annotations[0]);
    }

    public function testIfNestingAnnotationsWorks()
    {
        list($annotation) = $this->getDomainObjectAnnotations();
        $classPermissions = $annotation->getClassPermissions();

        $this->assertCount(1, $classPermissions);
        $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\ClassPermission', $classPermissions[0]);
    }

    public function testRemoveKeyInAnnotation()
    {
        $root = new Acl\DomainObject();

        $this->assertNull($root->getRemove());
    }

    public function testDataOfAnnotationSimple()
    {
        $root = new Acl\DomainObject(array(
            'remove' => false,
            'value' => array(
                new Acl\ClassPermission(array(
                    'value' => array(
                        'ROLE_USER' => 1
                    )
                ))
            )
        ));

        $this->assertFalse($root->getRemove());
        $this->assertCount(1, $root->getClassPermissions());

        foreach ($root->getClassPermissions() as $permission) {
            $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\ClassPermission', $permission);
            $this->assertTrue(is_array($permission->getRoles()));

            $roles = $permission->getRoles();

            foreach ($roles as $role) {
                $this->assertTrue(is_array($roles));

                $roleString = current(array_keys($roles));
                $mask = current(array_values($roles));

                $this->assertEquals($roleString, 'ROLE_USER');
                $this->assertEquals($mask, 1);
            }
        }
    }

    public function testDataOfAnnotationComplex()
    {
        $root = new Acl\DomainObject(array(
            'remove' => true,
            'value' => array(
                new Acl\ClassPermission(array(
                    'value' => array(
                        'ROLE_USER' => 1,
                        'ROLE_ADMIN' => 2,
                    )
                )),
                new Acl\ClassPermission(array(
                    'value' => array(
                        'ROLE_SUPER_ADMIN' => 4,
                    )
                ))
            )
        ));

        $this->assertTrue($root->getRemove());
        $this->assertCount(2, $root->getClassPermissions());

        foreach ($root->getClassPermissions() as $permission) {
            $this->assertInstanceOf('Oneup\AclBundle\Mapping\Annotation\ClassPermission', $permission);
            $this->assertTrue(is_array($permission->getRoles()));

            $roles = $permission->getRoles();

            foreach ($roles as $role) {
                $this->assertTrue(is_array($roles));

                $roleString = current(array_keys($roles));
                $mask = current(array_values($roles));

                if ($roleString == 'ROLE_USER') {
                    $this->assertEquals($mask, 1);
                    continue;
                }

                if ($roleString == 'ROLE_ADMIN') {
                    $this->assertEquals($mask, 2);
                    continue;
                }

                if ($roleString == 'ROLE_SUPER_ADMIN') {
                    $this->assertEquals($mask, 4);
                    continue;
                }

                $this->fail();
            }
        }
    }

    protected function getDomainObjectAnnotations()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('Oneup\AclBundle\Mapping\Annotation');

        $object = new \ReflectionClass('Oneup\AclBundle\Tests\Model\SomeObject');

        return $reader->getClassAnnotations($object);
    }
}
