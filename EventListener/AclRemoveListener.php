<?php

namespace Oneup\AclBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Oneup\AclBundle\Security\Acl\Manager\AclManager;

class AclRemoveListener
{
    protected $reader;
    protected $manager;

    public function __construct($remove, Reader $reader, AclManager $manager)
    {
        $this->remove = $remove;
        $this->reader = $reader;
        $this->manager = $manager;
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        if (!$this->remove) {
            return;
        }

        $entity = $event->getEntity();
        $object = new \ReflectionClass($entity);

        $annotation = $this->reader->getClassAnnotation($object, 'Oneup\AclBundle\Annotation\DomainObject');

        if ($annotation && $annotation->removeAcl) {
            $this->manager->removeAllObjectPermissions($entity);
        }
    }
}
