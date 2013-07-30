<?php

namespace Oneup\AclBundle\Tests\Model;

use Oneup\AclBundle\Annotation\ObjectIdentity;

/**
 * @ObjectIdentity
 */
class DomainObject
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
