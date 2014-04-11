<?php

namespace Oneup\AclBundle\Tests\Model;

use Oneup\AclBundle\Mapping\Annotation\DomainObject;
use Oneup\AclBundle\Mapping\Annotation\ClassPermission;
use Oneup\AclBundle\Mapping\Annotation\PropertyPermission;

/**
 * Masks from Symfony\Component\Security\Acl\Permission\MaskBuilder
 *
 * @DomainObject(remove=true, {
 *   @ClassPermission({ "ROLE_USER" = 1 })
 * })
 */
class SomeOtherObject
{
    private $id;
    private $foo;
    private $bar;

    /**
     * @PropertyPermission({ "ROLE_ADMIN" = 512 })
     */
    private $secured;

    public function __construct($id, $foo = null, $bar = null)
    {
        $this->id = $id;
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
