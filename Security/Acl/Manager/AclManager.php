<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Domain\AclCollectionCache;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

use Oneup\AclBundle\Security\Acl\Model\AbstractAclManager;

class AclManager extends AbstractAclManager
{
    protected $cache;
    protected $provider;
    protected $token_storage;
    protected $authorization_checker;
    protected $objectIdentityStrategy;
    protected $permissionStrategy;

    public function __construct(
        MutableAclProviderInterface $provider,
        $token_storage,
        $authorization_checker,
        ObjectIdentityRetrievalStrategyInterface $objectIdentityStrategy,
        AclCollectionCache $cache,
        $permissionStrategy
    ) {
        $this->provider = $provider;
        $this->token_storage = $token_storage;
        $this->authorization_checker = $authorization_checker;
        $this->objectIdentityStrategy = $objectIdentityStrategy;
        $this->cache = $cache;
        $this->permissionStrategy = $permissionStrategy;
    }

    public function preload($objects, $token = null)
    {
        if (is_null($token)) {
            $token = $this->getCurrentAuthenticationToken();
        }

        $objects = is_array($objects) ? $objects : array($objects);
        $token = is_array($token) ? $token : array($token);

        return $this->cache->cache($objects, $token);
    }

    protected function getProvider()
    {
        return $this->provider;
    }

    protected function getTokenStorage()
    {
        return $this->token_storage;
    }

    protected function getAuthorizationChecker()
    {
        return $this->authorization_checker;
    }

    protected function getObjectIdentityStrategy()
    {
        return $this->objectIdentityStrategy;
    }

    protected function getPermissionStrategy()
    {
        return $this->permissionStrategy;
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
