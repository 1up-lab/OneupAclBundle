<?php

namespace Oneup\AclBundle\Mapping\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DomainObject
{
    protected $remove;
    protected $classPermissions;

    public function __construct($data = array())
    {
        $this->classPermissions = array();

        if (array_key_exists('remove', $data)) {
            $this->remove = (bool) $data['remove'];
        }

        if (array_key_exists('value', $data) && is_array($data['value'])) {
            foreach ($data['value'] as $sub) {
                if ($sub instanceof ClassPermission) {
                    $this->classPermissions[] = $sub;
                }
            }
        }
    }

    public function getRemove()
    {
        return $this->remove;
    }

    public function getClassPermissions()
    {
        return $this->classPermissions;
    }
}
