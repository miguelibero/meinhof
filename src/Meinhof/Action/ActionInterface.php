<?php

namespace Meinhof\Action;

interface ActionInterface
{
    /**
     * specifies the event that will call the action
     * @return string
     */
    public function getEventName();

    /**
     * specifies the action name
     * @return string
     */
    public function getName();


    /**
     * executes the action
     */
    public function take();
}