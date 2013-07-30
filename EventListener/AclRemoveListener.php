<?php

namespace Oneup\AclBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Oneup\AclBundle\Security\Acl\Manager\AclManager;

class AclRemoveListener
{
    protected $reader;
    protected $manager;

    public function __construct(Reader $reader, AclManager $manager)
    {
        $this->reader = $reader;
        $this->manager = $manager;
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $object = new \ReflectionClass($entity);

        $annotation = $this->reader->getClassAnnotation($object, 'Oneup\AclBundle\Annotation\DomainObject');

        if ($annotation && $annotation->removeAcl) {
            $this->manager->removeObjectPermissions($entity);
        }
    }
}
