<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

class SimpleEngine extends AbstractEngine
{
    protected function getName()
    {
        return 'simple';
    }

    /**
     * @{inheritdoc}
     */
    protected function parse(Storage $template, array $vars = array())
    {
        $params = array();
        foreach ($vars as $k=>$v) {
            $k = '%%'.$k.'%%';
            if (is_array($v)) {
                $v = implode(', ', $v);
            }
            $params[$k] = $v;
        }

        return strtr($template->getContent(), $params);
    }
}
