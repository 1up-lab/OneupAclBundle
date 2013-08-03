<?php

namespace Oneup\AclBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Oneup\AclBundle\Security\Acl\Manager\AclManager;

class AclInsertListener
{
    protected $reader;
    protected $manager;

    public function __construct(Reader $reader, AclManager $manager)
    {
        $this->remove = $remove;
        $this->reader = $reader;
        $this->manager = $manager;
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $object = new \ReflectionClass($entity);

        $annotation = $this->reader->getClassAnnotation($object, 'Oneup\AclBundle\Annotation\DomainObject');

        if ($annotation) {
            foreach ($annotation->getClassPermissions() as $classPermission) {
                foreach ($classPermission->getRoles() as $role => $mask) {
                    $this->manager->addClassPermission($entity, $mask, $role);
                }
            }
        }
    }
}
