<?php

namespace Oneup\AclBundle\Security\Acl\Model;

interface AclManagerInterface
{
    public function grantObjectPermission($object, $identity, $mask);
    public function setObjectPermission($object, $identity, $mask);
    public function revokeObjectPermission($object, $identity, $mask);
    public function revokeObjectPermissions($object, $identity);
    public function revokeAllObjectPermissions($object);

    public function grantObjectFieldPermission($object, $field, $identity, $mask);
    public function setObjectFieldPermission($object, $field, $identity, $mask);
    public function revokeObjectFieldPermission($object, $field, $identity, $mask);
    public function revokeObjectFieldPermissions($object, $field, $identity);
    public function revokeAllObjectFieldPermissions($object);

    public function grantClassPermission($object, $identity, $mask);
    public function setClassPermission($object, $identity, $mask);
    public function revokeClassPermission($object, $identity, $mask);
    public function revokeClassPermissions($object, $identity);
    public function revokeAllClassPermissions($object);

    public function grantClassFieldPermission($object, $field, $identity, $mask);
    public function setClassFieldPermission($object, $field, $identity, $mask);
    public function revokeClassFieldPermission($object, $field, $identity, $mask);
    public function revokeClassFieldPermissions($object, $field, $identity);
    public function revokeAllClassFieldPermissions($object);

    public function isGranted($attributes, $object = null);
}
