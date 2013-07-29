<?php

namespace Oneup\AclBundle\Tests\Model;

use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Dbal\Schema;
use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;
use Symfony\Component\Security\Acl\Voter\AclVoter;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Doctrine\DBAL\DriverManager;
use Oneup\AclBundle\Security\Acl\Manager\AclManager;

abstract class AbstractSecurityTest extends \PHPUnit_Framework_TestCase
{
    private $connection;

    public function testConnectionEstablished()
    {
        $this->assertNotNull($this->connection);
    }

    protected function setUp()
    {
        if (!class_exists('Doctrine\DBAL\DriverManager')) {
            $this->markTestSkipped('The Doctrine2 DBAL is required for this test');
        }
    
        if (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('This test requires SQLite support in your environment');
        }

        $this->connection = DriverManager::getConnection(array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ));
        
        $options = array(
            'oid_table_name' => 'acl_object_identities',
            'oid_ancestors_table_name' => 'acl_object_identity_ancestors',
            'class_table_name' => 'acl_classes',
            'sid_table_name' => 'acl_security_identities',
            'entry_table_name' => 'acl_entries',
        );

        $schema = new Schema($options);

        foreach ($schema->toSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
        
        $oiStrategy = new ObjectIdentityRetrievalStrategy();
        $pgStrategy = new PermissionGrantingStrategy();
        //$siStrategy = new SecurityIdentityRetrievalStrategy();
        
        $aclProvider =  new MutableAclProvider($this->connection, $pgStrategy, $options);

        // create security context
        $permissionMap = new BasicPermissionMap();
        $aclVoter = new AclVoter($aclProvider, $oiStrategy);//, $pgStrategy, $permissionMap);
        $accessDecisionManager = new AccessDecisionManager(array($aclVoter));
        $authenticationManager = new AuthenticationProviderManager(array(
            new AnonymousAuthenticationProvider('oneup_acl')
        ));
        
        $securityContext = new SecurityContext($authenticationManager, $accessDecisionManager);
        
        // and now finally create our acl manager
        $this->aclManager = new AclManager($aclProvider, $securityContext, $oiStrategy);
    }

    protected function tearDown()
    {
        $this->connection = null;
    }
}