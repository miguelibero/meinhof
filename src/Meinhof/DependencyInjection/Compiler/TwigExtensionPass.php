<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TwigExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $post_env = $container->getDefinition('twig_environment.post');
        $view_env = $container->getDefinition('twig_environment.view');

        foreach ($container->findTaggedServiceIds('twig.extension') as $id => $attributes) {
            $type = $this->getTypeFromAttributes($attributes);
            $args = array(new Reference($id));
            if($post_env){
                $post_env->addMethodCall('addExtension', $args);
            }
            if($view_env){
                $view_env->addMethodCall('addExtension', $args);
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
