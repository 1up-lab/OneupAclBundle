<?php

namespace Oneup\AclBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Oneup\AclBundle\Configuration\ParamPermission;

class TestController extends Controller
{
    /**
     * @ParamPermission({ "one" = "VIEW" })
     */
    public function oneAction(SomeObject $one)
    {
        // ...
    }

    /**
     * @ParamPermission({
     *   "one" = "VIEW",
     *   "two" = "VIEW"
     *  })
     */
    public function twoAction(SomeObject $one, SomeObject $two)
    {
        // ...
    }

    /**
     * @ParamPermission({ "one" = "VIEW" })
     * @ParamPermission({ "two" = "VIEW" })
     */
    public function threeAction(SomeObject $one, SomeObject $two)
    {
        // ...
    }
}
