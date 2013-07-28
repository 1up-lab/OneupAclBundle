<?php

namespace Oneup\AclBundle\Security\Acl\Model;

interface AclManagerInterface
{
    public function addObjectPermission($object, $identity, $mask);
    public function setObjectPermission($object, $identity, $mask);
    public function revokeObjectPermission($object, $identity, $mask);

    public function addClassPermission($object, $identity, $mask);
    public function setClassPermission($object, $identity, $mask);
    public function revokeClassPermission($object, $identity, $mask);

    public function revokeObjectPermissions($object);
    public function revokeClassPermissions($object);
    public function revokePermissions($object);

    public function isGranted($attributes, $object = null);
}
