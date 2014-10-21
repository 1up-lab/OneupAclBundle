<?php

namespace Oneup\AclBundle\Security\Authorization\Acl;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * AclProvider
 * This class is "inspired" by this post:
 * http://stackoverflow.com/questions/20154865/find-aces-by-securityidentity-instead-of-objectidentity
 *
 * @uses MutableAclProvider
 */
class AclProvider extends MutableAclProvider
{
    /** @var Connection $connection */
    protected $connection;

    /** Locates all objects that the specified User has access to.
     *
     * Note that this method has a few limitations:
     *  - No support for filtering by mask.
     *  - No support for ACEs that match one of the User's roles (only ACEs that
     *      reference the User's security identity will be matched).
     *  - Every ACE that matches is assumed to grant access.
     *
     * @param UserInterface $identityObject
     * @param integer       $mask
     * @param string        $type   If set, filter by object type (classname).
     *
     * @return ObjectIdentity[]
     */
    public function findObjectIdentitiesForUser(
        $identityObject,
        $mask = MaskBuilder::MASK_VIEW,
        $type = null
    ) {
        /** @var UserSecurityIdentity $securityIdentity */
        $securityIdentity = $this->getSecurityEntity($identityObject);
        if (!$securityIdentity) {
            return null;
        }

        $identifier = sprintf(
            '%s-%s',
            $securityIdentity->getClass(),
            $securityIdentity->getUsername()
        );

        $sql = <<<END
            SELECT
              o.object_identifier
            , c.class_type
            FROM {$this->options['sid_table_name']} s
            LEFT JOIN {$this->options['entry_table_name']} e
                ON (
                        (e.security_identity_id = s.id)
                    or  {$this->connection->getDatabasePlatform()->getIsNullExpression('e.security_identity_id')}
                )
            LEFT JOIN {$this->options['oid_table_name']} o
                ON (o.id = e.object_identity_id)
            LEFT JOIN {$this->options['class_table_name']} c
                ON (c.id = o.class_id)
            WHERE s.identifier = {$this->connection->quote($identifier)}
            AND e.mask >= {$mask}
END;

        if ($type) {
            $sql .= <<<END
        AND c.class_type = {$this->connection->quote($type)}
END;
        }

        $objectIdentities = array();

        /* It would be awesome if we could use hydrateObjectIdentities()
         * here.  Then we could do super fancy stuff like filter by mask and
         * check whether ACEs grant or deny access.
         *
         * Unfortunately, that method is not accessible to subclasses.
         */
        $results = $this->connection->executeQuery($sql)->fetchAll();
        foreach ($results as $row) {
            $objectIdentities[] = new ObjectIdentity(
                $row['object_identifier'],
                $row['class_type']
            );
        }

        return $objectIdentities;
    }

    /** Locates all objects that the specified Role has access to.
     *
     * Note that this method has a few limitations:
     *  - No support for filtering by mask.
     *  - No support for ACEs that match one of the User's roles (only ACEs that
     *      reference the User's security identity will be matched).
     *  - Every ACE that matches is assumed to grant access.
     *
     * @param RoleInterface $identityObject
     * @param integer       $mask
     * @param string        $type   If set, filter by object type (classname).
     *
     * @return ObjectIdentity[]
     */
    public function findObjectIdentitiesForRole(
        $identityObject,
        $mask = MaskBuilder::MASK_VIEW,
        $type = null
    ) {
        /** @var RoleSecurityIdentity $securityIdentity */
        $securityIdentity = $this->getSecurityEntity($identityObject);
        if (!$securityIdentity) {
            return null;
        }

        $identifier = $securityIdentity->getRole();

        $sql = <<<END
            SELECT
              o.object_identifier
            , c.class_type
            FROM {$this->options['sid_table_name']} s
            LEFT JOIN {$this->options['entry_table_name']} e
                ON (
                        (e.security_identity_id = s.id)
                    or  {$this->connection->getDatabasePlatform()->getIsNullExpression('e.security_identity_id')}
                )
            LEFT JOIN {$this->options['oid_table_name']} o
                ON (o.id = e.object_identity_id)
            LEFT JOIN {$this->options['class_table_name']} c
                ON (c.id = o.class_id)
            WHERE s.identifier = {$this->connection->quote($identifier)}
            AND e.mask >= {$mask}
END;

        if ($type) {
            $sql .= <<<END
        AND c.class_type = {$this->connection->quote($type)}
END;
        }

        $objectIdentities = array();

        /* It would be awesome if we could use hydrateObjectIdentities()
         * here.  Then we could do super fancy stuff like filter by mask and
         * check whether ACEs grant or deny access.
         *
         * Unfortunately, that method is not accessible to subclasses.
         */
        $results = $this->connection->executeQuery($sql)->fetchAll();
        foreach ($results as $row) {
            $objectIdentities[] = new ObjectIdentity(
                $row['object_identifier'],
                $row['class_type']
            );
        }

        return $objectIdentities;
    }


    /**
     * getSecurityEntity
     *
     * @param mixed $identityObject
     * @access private
     * @return SecurityIdentityInterface
     */
    private function getSecurityEntity($identityObject)
    {
        if ($identityObject instanceof UserInterface) {
            return UserSecurityIdentity::fromAccount($identityObject);
        } elseif ($identityObject instanceof TokenInterface) {
            return UserSecurityIdentity::fromToken($identityObject);
        } elseif ($identityObject instanceof RoleInterface) {
            return new RoleSecurityIdentity($identityObject->getRole());
        }

        return null;
    }
}
