<?php

namespace Meinhof\Assetic;

interface ResourceLoader
{
    /**
     * @return string
     */
    public function getResourceType($name);
  
    /**
     * @return Assetic\Factory\Resource\ResourceInterface
     */
    public function getResource($name);
}