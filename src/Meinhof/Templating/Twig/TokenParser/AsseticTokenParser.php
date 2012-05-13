<?php

namespace Meinhof\Templating\Twig\TokenParser;

use Assetic\Extension\Twig\AsseticTokenParser as BaseAsseticTokenParser;
use Assetic\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;

class AsseticTokenParser extends BaseAsseticTokenParser
{
    private $attributes;

    public function setNodeAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    protected function createNode(AssetInterface $asset, \Twig_NodeInterface $body, array $inputs, array $filters, $name, array $attributes = array(), $lineno = 0, $tag = null)
    {        
        $attributes = array_merge($this->attributes, $attributes);
        return parent::createNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
    }
}
