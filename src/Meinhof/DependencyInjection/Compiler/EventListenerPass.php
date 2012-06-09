<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Add new event listeners using the `event_listener`tag.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class EventListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $def = $container->getDefinition('event_dispatcher');
        foreach ($container->findTaggedServiceIds('event_listener') as $id => $tags) {
            foreach ($tags as $attributes) {
                $event = $this->getEventFromAttributes($attributes);
                $method = $this->getMethodFromAttributes($attributes);
                $priority = $this->getPriorityFromAttributes($attributes);
                $def->addMethodCall('addListenerService', array($event, array($id, $method), $priority));
            }
        }
    }

    /**
     * Returns the event name from the tag attributes.
     *
     * @param mixed $attrs tag attributes
     *
     * @return string event name
     *
     * @throws \InvalidArgumentException if the attributes are invalid
     */
    protected function getEventFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['event'])) {
            throw new \InvalidArgumentException("Event listener without an event.");
        }

        return $attrs['event'];
    }

    /**
     * Returns the event method from the tag attributes.
     *
     * @param mixed $attrs tag attributes
     *
     * @return string event method
     */
    protected function getMethodFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['method'])) {
            return $this->getEventFromAttributes($attrs);
        }

        return $attrs['method'];
    }

    /**
     * Returns the event priority from the tag attributes.
     * If no priority specified returns priority 0.
     *
     * @param mixed $attrs tag attributes
     *
     * @return string event priotity
     */
    protected function getPriorityFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['priority'])) {
            return 0;
        }
        $p = intval($attrs['priority']);

        return $p < 0 ? 0 : $p;
    }
}
