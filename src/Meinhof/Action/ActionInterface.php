<?php

namespace Meinhof\Action;

interface ActionInterface
{
    /**
     * executes the action
     */
    public function take();
}