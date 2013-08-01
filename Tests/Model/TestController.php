<?php

namespace Oneup\AclBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Oneup\AclBundle\Annotation\AclCheck;
use Oneup\AclBundle\Tests\Model\SomeObject;

class TestController extends Controller
{
    /**
     * @AclCheck({ "one" = 128})
     */
    public function indexAction(SomeObject $one)
    {
        // ...
    }
}
