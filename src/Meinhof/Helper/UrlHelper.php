<?php

namespace Meinhof\Helper;

use Symfony\Component\DependencyInjection\Container;

/**
 * Helper class that returns an url for a given model.
 * THe helper class uses a url template in the form of:
 * `/posts/{date|Y-m-d}/{slug}`
 * Where the variables are resolved using PropertyPath on
 * the passed model.
 */
class UrlHelper implements UrlHelperInterface
{
    protected $template;
    protected $parameters;

    const PARAMETER_REGEX = '/{(?P<name>.*)(\|(?P<format>.*))?}/U';

    public function __construct($template, array $parameters=array())
    {
        $this->template = $template;
        $this->parameters = $parameters;
    }

    public function stringify($value, $format)
    {
        if ($value instanceof \DateTime) {
            if (!$format) {
                $format = 'Y-m-d';
            }

            return $value->format($format);
        }
        if (!$format) {
            $format = "%s";
        }

        return sprintf($format, $value);
    }

    public function getUrl($model, array $parameters)
    {
        try{
            $url = $this->getPropertyPath($model, 'url');
            if(is_string($url) && $url){
                return $url;
            }
        }catch(\Exception $e){
        }
        $url = $this->template;
        $parameters = array_merge($parameters, $this->parameters);

        preg_match_all(self::PARAMETER_REGEX, $this->template, $m);

        if (!isset($m['name']) || !is_array($m['name'])) {
            return $url;
        }
        if (!isset($m[0]) || !is_array($m[0])) {
            return $url;
        }
        foreach ($m[0] as $k=>$str) {
            if (!isset($m['name'][$k])) {
                continue;
            }
            $name = $m['name'][$k];
            $format = null;
            if (isset($m['format'][$k])) {
                $format = $m['format'][$k];
            }
            if (isset($parameters[$name])) {
                $value = $parameters[$name];
            } else {
                $value = $this->getPropertyPath($model, $name);
            }
            $value = $this->stringify($value, $format);
            $url = str_replace($str, $value, $url);
        }

        return $url;
    }

    protected function getPropertyPath($model, $path, $sep = '.')
    {
        $parts = explode($sep, $path);
        $name = reset($parts);
        $path = implode($sep, array_slice($parts, 1));
        $part = null;
        $found = false;
        if (is_array($model)) {
            if (isset($model[$name])) {
                $part = $model[$name];
                $found = true;
            }
        } elseif (is_object($model)) {
            $method = 'get'.Container::camelize($name);
            if (is_callable(array($model, $method))) {
                $part = $model->$method();
                $found = true;
            }
        }
        if ($found) {
            if ($path) {
                return $this->getPropertyPath($part, $path, $sep);
            } else {
                return $part;
            }
        } else {
            throw new \RuntimeException("Could not find part $name.");
        }
    }
}
