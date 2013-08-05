<?php

namespace Oneup\AclBundle\Tests\Model;

use Oneup\AclBundle\Annotation\DomainObject;
use Oneup\AclBundle\Annotation\ClassPermissions;
use Oneup\AclBundle\Annotation\PropertyPermission;

/**
 * @DomainObject(remove=true, {
 *   @ClassPermissions({ "ROLE_USER" = 1 })
 * })
 */
class SomeObject
{
    private $id;
    private $foo;
    private $bar;

    /**
     * @PropertyPermission({ "ROLE_ADMIN" = 1 })
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
