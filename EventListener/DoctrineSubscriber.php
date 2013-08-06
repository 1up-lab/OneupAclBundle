<?php

namespace Oneup\AclBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oneup\AclBundle\Driver\DriverChain;
use Oneup\AclBundle\Security\Acl\Model\AclManagerInterface;

class DoctrineSubscriber implements EventSubscriber
{
    protected $chain;
    protected $remove;
    protected $manager;

    public function __construct(DriverChain $chain, AclManagerInterface $manager, $remove)
    {
        $this->chain = $chain;
        $this->manager = $manager;
        $this->remove = $remove;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $object = new \ReflectionClass($entity);

        $metaData = $this->chain->readMetaData($object);

        if (!empty($metaData)) {
            // add class permissions
            foreach ($metaData['permissions'] as $permission) {
                $this->manager->addClassPermission($entity, $permission[1], $permission[0]);
            }

            // add property permissions
            foreach ($metaData['properties'] as $name => $property) {
                $this->manager->addClassFieldPermission($entity, $name, $property[1], $property[0]);
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

        $metaData = $this->chain->readMetaData($object);

        if (!empty($metaData) && ($this->remove || !!$metaData['remove'])) {
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
