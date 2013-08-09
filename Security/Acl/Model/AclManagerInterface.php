<?php

namespace Oneup\AclBundle\Security\Acl\Model;

interface AclManagerInterface
{
    public function addObjectPermission($object, $mask, $identity = null);
    public function setObjectPermission($object, $mask, $identity = null);
    public function revokeObjectPermission($object, $mask, $identity = null);
    public function revokeObjectPermissions($object, $identity = null);
    public function revokeAllObjectPermissions($object);

    public function addObjectFieldPermission($object, $field, $mask, $identity = null);
    public function setObjectFieldPermission($object, $field, $mask, $identity = null);
    public function revokeObjectFieldPermission($object, $field, $mask, $identity = null);
    public function revokeObjectFieldPermissions($object, $field, $identity = null);
    public function revokeAllObjectFieldPermissions($object);

    public function addClassPermission($object, $mask, $identity = null);
    public function setClassPermission($object, $mask, $identity = null);
    public function revokeClassPermission($object, $mask, $identity = null);
    public function revokeClassPermissions($object, $identity = null);
    public function revokeAllClassPermissions($object);

    public function addClassFieldPermission($object, $field, $mask, $identity = null);
    public function setClassFieldPermission($object, $field, $mask, $identity = null);
    public function revokeClassFieldPermission($object, $field, $mask, $identity = null);
    public function revokeClassFieldPermissions($object, $field, $identity = null);
    public function revokeAllClassFieldPermissions($object);

    public function isGranted($attributes, $object = null, $field = null);
}
