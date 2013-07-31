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

    public function addClassFieldPermission($object, $field, $identity, $mask);
    public function setClassFieldPermission($object, $field, $identity, $mask);
    public function revokeClassFieldPermission($object, $field, $identity, $mask);

    public function addObjectFieldPermission($object, $field, $identity, $mask);
    public function setObjectFieldPermission($object, $field, $identity, $mask);
    public function revokeObjectFieldPermission($object, $field, $identity, $mask);

    public function revokeObjectPermissions($object, $identity);
    public function revokeClassPermissions($object, $identity);
    public function revokeClassFieldPermissions($object, $field, $identity);

    public function revokeAllClassPermissions($object);
    public function revokeAllObjectPermissions($object);

    public function revokeAllObjectFieldPermissions($object);
    public function revokeAllClassFieldPermissions($object);

    public function isGranted($attributes, $object = null);
}
