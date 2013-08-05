<?php

namespace Oneup\AclBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineSubscriber implements EventSubscriber
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $reader  = $this->container->get('annotation_reader');
        $manager = $this->container->get('oneup_acl.manager');

        $entity = $args->getEntity();
        $object = new \ReflectionClass($entity);

        $annotation = $reader->getClassAnnotation($object, 'Oneup\AclBundle\Annotation\DomainObject');

        if ($annotation) {
            foreach ($annotation->getClassPermissions() as $classPermission) {
                foreach ($classPermission->getRoles() as $role => $mask) {
                    $manager->addClassPermission($entity, $mask, $role);
                }
            }

            $properties = $object->getProperties();

            foreach ($properties as $property) {
                $propAnnotation = $reader->getPropertyAnnotation($property, 'Oneup\AclBundle\Annotation\PropertyPermissions');

                if ($propAnnotation) {
                    foreach ($propAnnotation->getRoles() as $role => $mask) {
                        $manager->addClassFieldPermission($entity, $property->getName(), $mask, $role);
                    }
                }
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        if (!$this->remove) {
            return;
        }

        $entity = $args->getEntity();
        $object = new \ReflectionClass($entity);

        $annotation = $this->reader->getClassAnnotation($object, 'Oneup\AclBundle\Annotation\DomainObject');

        if ($annotation && ($annotation->getRemove() && $this->remove)) {
            $this->manager->revokeAllObjectPermissions($entity);
            $this->manager->revokeAllObjectFieldPermissions($entity);
        }
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'preRemove'
        );
    }
}
