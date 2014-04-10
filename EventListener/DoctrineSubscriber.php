<?php

namespace Oneup\AclBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineSubscriber implements EventSubscriber
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postPersist(EventArgs $args)
    {
        $chain = $this->container->get('oneup_acl.driver_chain');
        $manager = $this->container->get('oneup_acl.manager');

        if ($args instanceof \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs) {
            $entity = $args->getDocument();
        } else {
            $entity = $args->getObject();
        }

        $object = new \ReflectionClass($entity);
        $metaData = $chain->readMetaData($object);

        if (!empty($metaData)) {
            // add class permissions
            foreach ($metaData['permissions'] as $permission) {
                $manager->addClassPermission($entity, $permission[1], $permission[0]);
            }

            // add property permissions
            foreach ($metaData['properties'] as $name => $property) {
                $manager->addClassFieldPermission($entity, $name, $property[1], $property[0]);
            }
        }
    }

    public function preRemove(EventArgs $args)
    {
        $chain = $this->container->get('oneup_acl.driver_chain');
        $manager = $this->container->get('oneup_acl.manager');
        $remove = $this->container->getParameter('oneup_acl.remove_orphans');

        if ($args instanceof \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs) {
            $entity = $args->getDocument();
        } else {
            $entity = $args->getObject();
        }

        $object = new \ReflectionClass($entity);
        $metaData = $chain->readMetaData($object);

        if (($remove && (!isset($metaData['remove']) || $metaData['remove'])) ||
            (!$remove && isset($metaData['remove']) && $metaData['remove'])
        ) {
            $manager->revokeAllObjectPermissions($entity);
            $manager->revokeAllObjectFieldPermissions($entity);
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
