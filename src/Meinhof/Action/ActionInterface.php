<?php

namespace Meinhof\Action;

/**
 * Represents an action that is called on an event.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
interface ActionInterface
{
    /**
     * executes the action
     */
    public function take();
}
