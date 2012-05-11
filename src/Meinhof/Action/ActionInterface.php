<?php

namespace Meinhof\Action;

/**
 * Represents an action that is called on an event.
 */
interface ActionInterface
{
    /**
     * executes the action
     */
    public function take();
}