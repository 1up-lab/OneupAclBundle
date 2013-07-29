<?php

namespace Oneup\AclBundle\Tests\Model;

/*
use Symfony\Component\Security\Acl\Dbal\Schema;
*/

use Symfony\Component\Security\Acl\Dbal\Schema;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractSecurityTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $manager;

    public function testIfContainerExists()
    {
        $this->assertNotNull($this->client);
        $this->assertNotNull($this->container);
    }

    public function testIfSecurityContextLoads()
    {
        $aclProvider = $this->container->get('security.context');
        $this->assertTrue($aclProvider->isGranted('ROLE_USER'));
        $this->assertFalse($aclProvider->isGranted('ROLE_ADMIN'));
    }

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();

        $this->token = new UsernamePasswordToken('bob', null, 'main', array('ROLE_USER'));
        $this->container->get('security.context')->setToken($this->token);

        $this->connection = $this->container->get('database_connection');

        if (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('This test requires SQLite support in your environment.');
        }

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

        $this->manager = $this->container->get('oneup_acl.manager');
    }

    protected function getManager()
    {
        return $this->manager;
    }

    protected function getToken()
    {
        return $this->token;
    }
}
