<?php

namespace Oneup\AclBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ObjectIdentity
{
    public $removeAcl = true;
}
