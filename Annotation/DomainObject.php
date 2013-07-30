<?php

namespace Oneup\AclBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DomainObject
{
    public $removeAcl = true;
}
