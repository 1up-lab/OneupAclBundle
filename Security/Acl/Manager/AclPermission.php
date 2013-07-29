<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

class AclPermission
{
    protected $grant;
    protected $type;
    protected $token;
    protected $object;
    protected $mask;

    public function revoke($token)
    {
        if (!is_null($this->grant)) {
            throw new \BadMethodCallException('You can only call grant/revoke once.');
        }

        $this->grant = false;
        $this->token = $token;

        return $this;
    }

    public function grant($token)
    {
        if (!is_null($this->grant)) {
            throw new \BadMethodCallException('You can only call grant/revoke once.');
        }

        $this->grant = true;
        $this->token = $token;

        return $this;
    }

    public function accessTo($object)
    {
        if (!is_null($this->object)) {
            throw new \BadMethodCallException('You can only call accessTo/accessToAll once.');
        }

        $this->object = $object;
        $this->type = 'object';

        return $this;
    }

    public function accessToAll($object)
    {
        if (!is_null($this->object)) {
            throw new \BadMethodCallException('You can only call accessTo/accessToAll once.');
        }

        $this->object = $object;
        $this->type = 'class';
    }

    public function with($mask)
    {
        $this->mask = $mask;

        return $this;
    }

    public function compile()
    {
        return array(
            $this->grant,
            $this->type,
            $this->token,
            $this->object,
            $this->mask
        );
    }
}
