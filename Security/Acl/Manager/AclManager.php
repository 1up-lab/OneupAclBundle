<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Oneup\AclBundle\Security\Acl\Model\AbstractAclManager;

class AclManager extends AbstractAclManager
{
    protected $provider;
    protected $context;
    protected $strategy;

    public function __construct(
        MutableAclProviderInterface $provider,
        SecurityContextInterface $context,
        ObjectIdentityRetrievalStrategyInterface $strategy
    ) {
        $this->provider = $provider;
        $this->context = $context;
        $this->strategy = $strategy;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function getSecurityContext()
    {
        return $this->context;
    }

    public function getObjectIdentityStrategy()
    {
        return $this->strategy;
    }

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
                $this->grantPermission($object, $identity, $mask, $type) :
                $this->revokePermission($object, $identity, $mask, $type)
            ;
        } else {
            if (!$grant) {
                $this->revokePermissions($object, $identity);
            }
        }
    }
}
