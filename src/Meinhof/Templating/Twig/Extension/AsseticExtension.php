<?php

namespace Meinhof\Templating\Twig\Extension;

use Assetic\Extension\Twig\AsseticExtension as BaseAsseticExtension;
use Assetic\Extension\Twig\AsseticFilterInvoker;
use Meinhof\Templating\Twig\TokenParser\AsseticTokenParser;

class AsseticExtension extends BaseAsseticExtension
{
    protected $options;

    public function setDefaultAssetOptions(array $options)
    {
        $this->options = $options;
    }

    public function getTokenParsers()
    {
        $parsers = array(
            new AsseticTokenParser($this->factory, 'javascripts', 'js/*.js'),
            new AsseticTokenParser($this->factory, 'stylesheets', 'css/*.css'),
            new AsseticTokenParser($this->factory, 'image', 'images/*', true),
        );
        foreach($parsers as $parser){
            if($parser instanceof AsseticTokenParser){
                $parser->setNodeAttributes($this->options);
            }
        }
        return $parsers;
    }

    public function getFilterInvoker($function)
    {
        $filter = array();
        if(isset($this->functions[$function]) && is_array($this->functions[$function])){
            $filter = array_merge($filter, $this->functions[$function]);
        }
        if(!isset($filter['options']) || !is_array($filter['options'])){
            $filter['options'] = array();
        }
        $filter['options'] = array_merge($this->options, $filter['options']);
        return new AsseticFilterInvoker($this->factory, $filter);
    }

}