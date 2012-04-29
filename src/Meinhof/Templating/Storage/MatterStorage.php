<?php

namespace Meinhof\Templating\Storage;

use Symfony\Component\Templating\Storage\Storage;

abstract class MatterStorage extends Storage
{
    abstract public function getMatter();
}