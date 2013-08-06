<?php

namespace Oneup\AclBundle\Mapping\Annotation;

abstract class AbstractPermission
{
    protected $roles;

    public function __construct($roles)
    {
        $roles = array_key_exists('value', $roles) ? $roles['value'] : $roles;
        $this->setRoles($roles);
    }

    public function setRoles($roles)
    {
        $this->roles = is_array($roles) ? $roles : array($roles);
    }

    public function getRoles()
    {
        return $this->roles;
    }
}
