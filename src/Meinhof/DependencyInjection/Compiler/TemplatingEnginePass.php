<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TemplatingEnginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $post_def = $container->getDefinition('templating.post');
        $view_def = $container->getDefinition('templating.view');

        foreach ($container->findTaggedServiceIds('templating.engine') as $id => $attributes) {
            $type = $this->getTypeFromAttributes($attributes);
            $args = array(new Reference($id));
            switch($type){
                case 'post':
                    $post_def->addMethodCall('addEngine', $args);
                    break;
                case 'view':
                    $view_def->addMethodCall('addEngine', $args);
                    break;
                case 'all':
                    $post_def->addMethodCall('addEngine', $args);
                    $view_def->addMethodCall('addEngine', $args);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid templating engine type '${type}'.");
            }
        }
    }

    protected function getTypeFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['type'])){
            return 'all';
        }
        return $attrs[0]['type'];
    }
}