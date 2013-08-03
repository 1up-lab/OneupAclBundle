<?php

namespace Oneup\AclBundle\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ParamPermission implements ConfigurationInterface
{
    protected $entries = array();

    public function __construct($entries)
    {
        $this->setEntries($entries['value']);
    }

    public function getEntry($name)
    {
        return array_key_exists($name, $this->entries) ? $this->entries[$name] : null;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function setEntries($entries)
    {
        $this->entries = is_array($entries) ? $entries : array($entries);
    }

    public function setValue($entries)
    {
        $this->setEntries($entries['value']);
    }

    public function getAliasName()
    {
        return 'acl_permission';
    }

    public function allowArray()
    {
        return true;
    }
}
