<?php

namespace Oneup\AclBundle\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Oneup\AclBundle\Security\Acl\Model\AclManagerInterface;

class PermissionListener implements EventSubscriberInterface
{
    protected $manager;

    public function __construct(AclManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $request = $event->getRequest();

        if (!$configuration = $request->attributes->get('_acl_permission')) {
            return;
        }

        $refl = new \ReflectionMethod($controller[0], $controller[1]);

        foreach ($refl->getParameters() as $param) {
            if (!$param->getClass() || $param->getClass()->isInstance($request)) {
                continue;
            }

            $name = $param->getName();
            $object = $request->get($name);

            if (is_null($object)) {
                continue;
            }

            $mask = null;

            foreach ($configuration as $config) {
                if (!is_null($mask = $config->getEntry($name))) {
                    continue;
                }
            }

            if (is_null($mask)) {
                continue;
            }

            if (!$this->manager->isGranted($mask, $object)) {
                throw new AccessDeniedException('Acl permission for this object is not granted.');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}
