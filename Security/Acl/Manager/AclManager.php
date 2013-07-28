<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Util\ClassUtils;

use Oneup\AclBundle\Security\Acl\Model\AclManagerInterface;

class AclManager implements AclManagerInterface
{
    protected $provider;
    protected $context;
    protected $strategy;

    public function __construct(
        MutableAclProviderInterface $provider,
        SecurityContextInterface $securityContext,
        ObjectIdentityRetrievalStrategyInterface $identityRetrievalStrategy
    ) {
        $this->provider = $provider;
        $this->context = $context;
        $this->strategy = $identityRetrievalStrategy;
    }

    public function isGranted($attributes, $object = null)
    {
        return $this->context->isGranted($attributes, $object);
    }

    protected function createSecurityContext($input)
    {
        $identity = null;

        if ($input instanceof UserInterface) {
            $identity = UserSecurityIdentity::fromAccount($input);
        } elseif ($input instanceof TokenInterface) {
            $identity = UserSecurityIdentity::fromToken($identity);
        } elseif ($input instanceof RoleInterface || is_string($input)) {
            $identity = new RoleSecurityIdentity($identity);
        }

        if (!$identity instanceof SecurityIdentityInterface) {
            throw new \InvalidArgumentException('Couldn\'t create a valid SecurityIdentity with the provided identity information');
        }

        return $identity;
    }

    protected function createObjectIdentity($object)
    {
        if (is_object($object)) {
            return ObjectIdentity::fromDomainObject($object);
        }

        if (is_string($object)) {
            return new ObjectIdentity('class', ClassUtils::getRealClass(get_class($object)));
        }

        throw new \InvalidArgumentException('Couldn\'t create a valid ObjectIdentity with the provided information');
    }
}
