<?php

namespace Oneup\AclBundle\Tests\Security\Acl\Manager;

use Symfony\Component\Security\Acl\Dbal\Schema;
use Doctrine\DBAL\DriverManager;

class AclManagerTest extends \PHPUnit_Framework_TestCase
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

        $schema = new Schema(array(
            'oid_table_name' => 'acl_object_identities',
            'oid_ancestors_table_name' => 'acl_object_identity_ancestors',
            'class_table_name' => 'acl_classes',
            'sid_table_name' => 'acl_security_identities',
            'entry_table_name' => 'acl_entries',
        ));

        foreach ($schema->toSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
    }

    protected function tearDown()
    {
        $this->connection = null;
    }
}
