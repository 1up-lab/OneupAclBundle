<?php

namespace Oneup\AclBundle\Security\Acl\Model;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Util\ClassUtils;

abstract class AbstractAclManager implements AclManagerInterface
{
    abstract protected function getProvider();
    abstract protected function getSecurityContext();
    abstract protected function getObjectIdentityStrategy();
    abstract protected function getPermissionStrategy();

    public function addObjectPermission($object, $mask, $identity = null)
    {
        $this->addPermission($object, $identity, $mask, 'object');
    }

    public function setObjectPermission($object, $mask, $identity = null)
    {
        $this->revokeObjectPermissions($object, $identity);
        $this->addPermission($object, $identity, $mask, 'object');
    }

    public function revokeObjectPermission($object, $mask, $identity = null)
    {
        $this->revokePermission($object, $identity, $mask, 'object');
    }

    public function revokeObjectPermissions($object, $identity = null)
    {
        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object, false);

        if (null === $acl) return;

        $aces = $acl->getObjectAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i--) {
            if ($securityIdentity->equals($aces[$i]->getSecurityIdentity())) {
                $acl->deleteObjectAce($i);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function revokeAllObjectPermissions($object)
    {
        $acl  = $this->getAclFor($object, false);

        if (null === $acl) return;

        $aces = $acl->getObjectAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i--) {
            $acl->deleteObjectAce($i);
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function addObjectFieldPermission($object, $field, $mask, $identity = null)
    {
        $this->addFieldPermission($object, $field, $identity, $mask, 'object');
    }

    public function setObjectFieldPermission($object, $field, $mask, $identity = null)
    {
        $this->revokeObjectFieldPermissions($object, $field, $identity);
        $this->addFieldPermission($object, $field, $identity, $mask, 'object');
    }

    public function revokeObjectFieldPermission($object, $field, $mask, $identity = null)
    {
        $this->revokeFieldPermission($object, $field, $identity, $mask, 'object');
    }

    public function revokeObjectFieldPermissions($object, $field, $identity = null)
    {
        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object, false);

        if (null === $acl) return;

        $fieldAces = $acl->getObjectFieldAces($field);

        $size = count($fieldAces) - 1;
        reset($fieldAces);

        for ($i = $size; $i >= 0; $i--) {
            if ($securityIdentity->equals($fieldAces[$i]->getSecurityIdentity())) {
                $acl->deleteObjectFieldAce($i, $field);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function revokeAllObjectFieldPermissions($object)
    {
        $acl = $this->getAclFor($object, false);

        if (null === $acl) return;

        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $field = $property->getName();
            $fieldAces = $acl->getObjectFieldAces($field);

            $size = count($fieldAces) - 1;
            reset($fieldAces);

            for ($i = $size; $i >= 0; $i--) {
                $acl->deleteObjectFieldAce($i, $field);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function addClassPermission($object, $mask, $identity = null)
    {
        $this->addPermission($object, $identity, $mask, 'class');
    }

    public function setClassPermission($object, $mask, $identity = null)
    {
        $this->revokeClassPermissions($object, $identity);
        $this->addPermission($object, $identity, $mask, 'class');
    }

    public function revokeClassPermission($object, $mask, $identity = null)
    {
        $this->revokePermission($object, $identity, $mask, 'class');
    }

    public function revokeClassPermissions($object, $identity = null)
    {
        $securityIdentity = $this->createSecurityIdentity($identity);

        if (is_object($object)) {
            $object = get_class($object);
        }

        $acl  = $this->getAclFor($object, false);

        if (null === $acl) return;

        $aces = $acl->getClassAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i--) {
            if ($securityIdentity->equals($aces[$i]->getSecurityIdentity())) {
                $acl->deleteClassAce($i);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function revokeAllClassPermissions($object)
    {
        if (is_object($object)) {
            $object = get_class($object);
        }

        $acl  = $this->getAclFor($object, false);

        if (null === $acl) return;

        $aces = $acl->getClassAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i--) {
            $acl->deleteClassAce($i);
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function addClassFieldPermission($object, $field, $mask, $identity = null)
    {
        $this->addFieldPermission($object, $field, $identity, $mask, 'class');
    }

    public function setClassFieldPermission($object, $field, $mask, $identity = null)
    {
        $this->revokeClassFieldPermissions($object, $field, $identity);
        $this->addFieldPermission($object, $field, $identity, $mask, 'class');
    }

    public function revokeClassFieldPermission($object, $field, $mask, $identity = null)
    {
        $this->revokeFieldPermission($object, $field, $identity, $mask, 'class');
    }

    public function revokeClassFieldPermissions($object, $field, $identity = null)
    {
        $securityIdentity = $this->createSecurityIdentity($identity);

        if (is_object($object)) {
            $object = get_class($object);
        }

        $acl  = $this->getAclFor($object, false);

        if (null === $acl) return;

        $fieldAces = $acl->getClassFieldAces($field);

        $size = count($fieldAces) - 1;
        reset($fieldAces);

        for ($i = $size; $i >= 0; $i--) {
            if ($securityIdentity->equals($fieldAces[$i]->getSecurityIdentity())) {
                $acl->deleteClassFieldAce($i, $field);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function revokeAllClassFieldPermissions($object)
    {
        $acl = $this->getAclFor($object);

        if (null === $acl) return;

        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $field = $property->getName();
            $fieldAces = $acl->getClassFieldAces($field);

            $size = count($fieldAces) - 1;
            reset($fieldAces);

            for ($i = $size; $i >= 0; $i--) {
                $acl->deleteClassFieldAce($i, $field);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    public function isGranted($attributes, $object = null, $field = null)
    {
        if (is_object($object)) {
            // pre-load acl
            $this->getAclFor($object);
        }

        if ($field) {
            $oid = $this->createObjectIdentity($object);
            $object = new FieldVote($oid, $field);
        }

        return $this->getSecurityContext()->isGranted($attributes, $object);
    }

    protected function revokePermissions($object, $identity)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Pass an object to remove all permission types.');
        }

        $this->revokeObjectPermissions($object, $identity);
        $this->revokeClassPermissions($object, $identity);
    }

    protected function getAclFor($object, $createAcl = true)
    {
        $acl = null;
        $identity = $this->createObjectIdentity($object);

        try {
            $acl = $this->getProvider()->findAcl($identity);
        } catch (AclNotFoundException $e) {
            if ($createAcl) {
                $acl = $this->getProvider()->createAcl($identity);
            }
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

        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object, false);
        $aces = $type == 'object' ? $acl->getObjectAces() : $acl->getClassAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i--) {
            if ($securityIdentity->equals($aces[$i]->getSecurityIdentity())) {
                $this->removeMask($i, $acl, $aces[$i], $mask, $type);
            }
        }

        $this->getProvider()->updateAcl($acl);
    }

    protected function revokeFieldPermission($object, $field, $identity, $mask, $type)
    {
        if ($type == 'class') {
            if (is_object($object)) {
                $object = get_class($object);
            }
        }

        $securityIdentity = $this->createSecurityIdentity($identity);

        $acl  = $this->getAclFor($object, false);
        $fieldAces = $type == 'object' ? $acl->getObjectFieldAces($field) : $acl->getClassFieldAces($field);

        $size = count($fieldAces) - 1;
        reset($fieldAces);

        for ($i = $size; $i >= 0; $i--) {
            if ($securityIdentity->equals($fieldAces[$i]->getSecurityIdentity())) {
                $this->removeFieldMask($i, $field, $acl, $fieldAces[$i], $mask, $type);
            }
        }

        $this->getProvider()->updateAcl($acl);
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

    protected function removeFieldMask($index, $field, $acl, $fieldAce, $mask, $type)
    {
        if ($type == 'object') {
            $acl->updateObjectFieldAce($index, $field, $fieldAce->getMask() & ~$mask);
        }

        if ($type == 'class') {
            $acl->updateClassFieldAce($index, $field, $fieldAce->getMask() & ~$mask);
        }
    }

    protected function revokeAllPermissions($object, $type)
    {
        if ($type == 'class') {
            if (is_object($object)) {
                $object = get_class($object);
            }
        }

        $acl  = $this->getAclFor($object, false);
        $aces = $acl->getClassAces();

        $size = count($aces) - 1;
        reset($aces);

        for ($i = $size; $i >= 0; $i--) {
            $acl->deleteClassAce($i);
        }

        $this->getProvider()->updateAcl($acl);
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
            $acl->insertObjectAce($securityIdentity, $mask, 0, true, $this->getPermissionStrategy());
        } elseif ($type == 'class') {
            $acl->insertClassAce($securityIdentity, $mask, 0, true, $this->getPermissionStrategy());
        } else {
            throw new \InvalidArgumentException('This AceType is not valid.');
        }

        $this->getProvider()->updateAcl($acl);
    }

    protected function addFieldPermission($object, $field, $identity, $mask, $type)
    {
        if ($type == 'class') {
            if (is_object($object)) {
                $object = get_class($object);
            }
        }

        $securityIdentity = $this->createSecurityIdentity($identity);
        $acl = $this->getAclFor($object);

        if ($type == 'object') {
            $acl->insertObjectFieldAce($field, $securityIdentity, $mask, 0, true, $this->getPermissionStrategy());
        } elseif ($type == 'class') {
            $acl->insertClassFieldAce($field, $securityIdentity, $mask, 0, true, $this->getPermissionStrategy());
        } else {
            throw new \InvalidArgumentException('This AceType is not valid');
        }

        $this->getProvider()->updateAcl($acl);
    }

    protected function createSecurityIdentity($input)
    {
        if (is_null($input)) {
            $input = $this->getCurrentAuthenticationToken();
        }

        $identity = null;

        if ($input instanceof UserInterface) {
            $identity = UserSecurityIdentity::fromAccount($input);
        } elseif ($input instanceof TokenInterface) {
            $identity = UserSecurityIdentity::fromToken($input);
        } elseif ($input instanceof RoleInterface) {
            $identity = new RoleSecurityIdentity($input->getRole());
        } elseif (is_string($input)) {
            $identity = new RoleSecurityIdentity($input);
        } elseif ($input instanceof SecurityIdentityInterface) {
            $identity = $input;
        }

        if (!$identity instanceof SecurityIdentityInterface) {
            throw new \InvalidArgumentException('Couldn\'t create a valid SecurityIdentity with the provided identity information');
        }

        return $identity;
    }

    protected function createObjectIdentity($object)
    {
        if (is_object($object)) {
            return $this->getObjectIdentityStrategy()->getObjectIdentity($object);
        }

        if (is_string($object)) {
            return new ObjectIdentity('class', ClassUtils::getRealClass($object));
        }

        throw new \InvalidArgumentException('Couldn\'t create a valid ObjectIdentity with the provided information');
    }

    protected function getCurrentAuthenticationToken()
    {
        $token = $this->getSecurityContext()->getToken();

        if (null === $token) {
            return null;
        }

        $user = $token->getUser();

        return (is_object($user)) ? $user : $token;
    }
}
