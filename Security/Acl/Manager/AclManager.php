<?php

namespace Oneup\AclBundle\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
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
        SecurityContextInterface $context,
        ObjectIdentityRetrievalStrategyInterface $strategy
    ) {
        $this->provider = $provider;
        $this->context = $context;
        $this->strategy = $strategy;
    }

    public function addObjectPermission($object, $identity, $mask)
    {
        $this->addPermission($object, $identity, $mask, 'object');
    }

    public function revokeObjectPermission($object, $identity, $mask)
    {
        $this->revokePermission($object, $identity, $mask, 'object');
    }

    public function setObjectPermission($object, $identity, $mask)
    {
        $this->revokeObjectPermissions($object, $identity);
        $this->addPermission($object, $identity, $mask, 'object');
    }

    public function addClassPermission($object, $identity, $mask)
    {
        $this->addPermission($object, $identity, $mask, 'class');
    }

    public function revokeClassPermission($object, $identity, $mask)
    {
        $this->revokePermission($object, $identity, $mask, 'class');
    }

    public function setClassPermission($object, $identity, $mask)
    {
        $this->revokeClassPermissions($object, $identity);
        $this->addPermission($object, $identity, $mask, 'class');
    }

    public function revokeObjectPermissions($object, $identity)
    {
        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object);
        $aces = $acl->getObjectAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i --) {
            if ($securityIdentity->equals($aces[$i]->getSecurityIdentity())) {
                $acl->deleteObjectAce($i);
            }
        }

        $this->provider->updateAcl($acl);
    }

    public function revokeClassPermissions($object, $identity)
    {
        if (is_object($object)) {
            $object = get_class($object);
        }

        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object);
        $aces = $acl->getClassAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i --) {
            if ($securityIdentity->equals($aces[$i]->getSecurityIdentity())) {
                $acl->deleteClassAce($i);
            }
        }

        $this->provider->updateAcl($acl);
    }

    public function revokePermissions($object, $identity)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Pass an object to remove all permission types.');
        }

        $this->revokeObjectPermissions($object, $identity);
        $this->revokeClassPermissions($object, $identity);
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

    public function isGranted($attributes, $object = null)
    {
        if (is_object($object)) {
            // preload acl
            $this->getAclFor($object);
        }

        return $this->context->isGranted($attributes, $object);
    }

    protected function getAclFor($object)
    {
        $identity = $this->createObjectIdentity($object);

        try {
            $acl = $this->provider->createAcl($identity);
        } catch (AclAlreadyExistsException $e) {
            $acl = $this->provider->findAcl($identity);
        }

        return $acl;
    }

    protected function revokePermission($object, $identity, $mask, $type)
    {
        if ($type == 'class') {
            if (is_object($object)) {
                $object = get_class($object);
            }
        }

        $objectIdentity = $this->createObjectIdentity($object);
        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object);
        $aces = $type == 'object' ? $acl->getObjectAces() : $acl->getClassAces();

        foreach ($aces as $key => $ace) {
            if ($securityIdentity->equals($ace->getSecurityIdentity())) {
                $this->removeMask($key, $acl, $ace, $mask, $type);
            }
        }

        $this->provider->updateAcl($acl);
    }

    protected function removeMask($index, $acl, $ace, $mask, $type)
    {
        if ($type == 'object') {
            $acl->updateObjectAce($index, $ace->getMask() & ~$mask);
        }

        if ($type == 'class') {
            $acl->updateClassAce($index, $ace->getMask() & ~$mask);
        }
    }

    protected function addPermission($object, $identity, $mask, $type)
    {
        if ($type == 'class') {
            if (is_object($object)) {
                $object = get_class($object);
            }
        }

        $securityIdentity = $this->createSecurityIdentity($identity);
        $acl = $this->getAclFor($object);

        if ($type == 'object') {
            $acl->insertObjectAce($securityIdentity, $mask);
        } elseif ($type == 'class') {
            $acl->insertClassAce($securityIdentity, $mask);
        } else {
            throw new \InvalidArgumentException('This AceType is not valid.');
        }

        $this->provider->updateAcl($acl);
    }

    protected function createSecurityIdentity($input)
    {
        $identity = null;

        if ($input instanceof UserInterface) {
            $identity = UserSecurityIdentity::fromAccount($input);
        } elseif ($input instanceof TokenInterface) {
            $identity = UserSecurityIdentity::fromToken($input);
        } elseif ($input instanceof RoleInterface || is_string($input)) {
            $identity = new RoleSecurityIdentity($input);
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

    protected function getCurrentAuthenticationToken()
    {
        $token = $this->context->getToken();

        if (!is_null($token)) {
            $token = $token->getUser();
        }

        return $token;
    }
}
