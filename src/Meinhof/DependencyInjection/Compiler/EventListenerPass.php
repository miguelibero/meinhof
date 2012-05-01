<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EventListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $def = $container->getDefinition('event_dispatcher');
        foreach ($container->findTaggedServiceIds('event_listener') as $id => $attributes) {
            $event = $this->getEventFromAttributes($attributes);
            $method = $this->getMethodFromAttributes($attributes);
            $priority = $this->getPriorityFromAttributes($attributes);
            if(!$event){
                throw new \InvalidArgumentException("Event listener '${id}' without an event.");
            }
            $def->addMethodCall('addListenerService', array($event, array($id, $method), $priority));
        }
    }

    protected function getEventFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['event'])){
            return null;
        }
        return $attrs[0]['event'];
    }

    protected function getMethodFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['method'])){
            return $this->getEventFromAttributes($attrs);
        }
        return $attrs[0]['method'];
    }

    protected function getPriorityFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['priority'])){
            return 0;
        }
        $p = intval($attrs[0]['priority']);
        return $p < 0 ? 0 : $p;
    }        
}