<?php

namespace Oneup\AclBundle\Tests\Model;

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
