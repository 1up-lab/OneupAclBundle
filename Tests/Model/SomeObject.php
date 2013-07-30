<?php

namespace Oneup\AclBundle\Tests\Model;

use Oneup\AclBundle\Annotation\DomainObject;

/**
 * @DomainObject
 */
class SomeObject
{
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
