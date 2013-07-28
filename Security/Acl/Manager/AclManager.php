<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
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

    public function addObjectPermission($object, $identity, $mask)
    {
        $this->addPermission($object, $identity, $mask, 'object');
    }

    public function addClassPermission($object, $identity, $mask)
    {
        $this->addPermission($object, $identity, $mask, 'class');
    }

    public function isGranted($attributes, $object = null)
    {
        return $this->context->isGranted($attributes, $object);
    }

    protected function addPermission($object, $identity, $mask, $type)
    {
        if ($type == 'class') {
            if (is_object($object)) {
                $object = get_class($object);
            }
        }

        $objectIdentity = $this->createObjectIdentity($object);
        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl = $this->provider->createAcl($objectIdentity);
        $acl->insertObjectAce($securityIdentity, $mask);

        $provider->updateAcl($acl);
    }

    protected function createSecurityIdentity($input)
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
            return $this->strategy->getObjectIdentity($object);
        }

        if (is_string($object)) {
            return new ObjectIdentity('class', ClassUtils::getRealClass($object));
        }

        throw new \InvalidArgumentException('Couldn\'t create a valid ObjectIdentity with the provided information');
    }
}
