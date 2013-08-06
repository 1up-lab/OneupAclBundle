<?php

namespace Oneup\AclBundle\Driver;

interface DriverInterface
{
    public function readMetaData(\ReflectionClass $class);
}
