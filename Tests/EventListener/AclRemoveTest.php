<?php

namespace Oneup\AclBundle\Tests\EventListener;

use Oneup\AclBundle\Tests\Model\AbstractSecurityTest;

class AclRemoveTest extends AbstractSecurityTest
{
    public function testIfEventListenerIsRegistered()
    {
        $this->assertTrue($this->container->has('oneup_acl.remove_listener'));
    }
    
    public function testIfEventListenerIsInstancable()
    {
        $listener = $this->container->get('oneup_acl.remove_listener');
        $this->assertInstanceOf('Oneup\AclBundle\EventListener\AclRemoveListener', $listener);
    }
}
