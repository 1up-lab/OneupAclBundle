<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

use Oneup\AclBundle\Security\Acl\Model\AbstractAclManager;

class AclManager extends AbstractAclManager
{
    public function grant($identity)
    {
        $object = new AclPermission($identity);
        $object->grant($identity);

        return $object;
    }

    public function revoke($identity)
    {
        $object = new AclPermission($identity);
        $object->revoke($identity);

        return $object;
    }

    public function compile(AclPermission $permission)
    {
        list($grant, $type, $identity, $object, $mask) = $permission->compile();

        if (is_null($identity)) {
            $identity = $this->getCurrentAuthenticationToken();
        }

        if (!is_null($mask)) {
            $grant ?
                $this->addPermission($object, $identity, $mask, $type) :
                $this->revokePermission($object, $identity, $mask, $type)
            ;
        } else {
            if (!$grant) {
                $this->revokePermissions($object, $identity);
            }
        }
    }
}
