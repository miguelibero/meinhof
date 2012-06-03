<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

/**
 * This simple engine replaces variables in a text.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
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
