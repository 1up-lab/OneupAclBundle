<?php

namespace Oneup\AclBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Oneup\AclBundle\Annotation\AclCheck;
use Oneup\AclBundle\Tests\Model\SomeObject;

class TestController extends Controller
{
    /**
     * @AclCheck({ "one" = "VIEW" })
     */
    public function oneAction(SomeObject $one)
    {
        // ...
    }
    
    /**
     * @AclCheck({
     *   "one" = "VIEW",
     *   "two" = "VIEW"
     *  })
     */
    public function twoAction(SomeObject $one)
    {
        // ...
    }

    /**
     * @AclCheck({ "one" = "VIEW" })
     * @AclCheck({ "two" = "VIEW" })
     */
    public function threeAction(SomeObject $one, SomeObject $two)
    {
        // ...
    }
}
