<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class EventListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $def = $container->getDefinition('event_dispatcher');
        foreach ($container->findTaggedServiceIds('event_listener') as $id => $tags) {
            foreach ($tags as $attributes) {
                $event = $this->getEventFromAttributes($attributes);
                $method = $this->getMethodFromAttributes($attributes);
                $priority = $this->getPriorityFromAttributes($attributes);
                if (!$event) {
                    throw new \InvalidArgumentException("Event listener '${id}' without an event.");
                }
                $def->addMethodCall('addListenerService', array($event, array($id, $method), $priority));
            }
        }
    }

    protected function getEventFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['event'])) {
            return null;
        }

        return $attrs['event'];
    }

    protected function getMethodFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['method'])) {
            return $this->getEventFromAttributes($attrs);
        }

        return $attrs['method'];
    }

    protected function getPriorityFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['priority'])) {
            return 0;
        }
        $p = intval($attrs['priority']);

        return $p < 0 ? 0 : $p;
    }
}
