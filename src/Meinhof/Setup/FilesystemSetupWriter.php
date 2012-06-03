<?php

namespace Meinhof\Setup;

use Symfony\Component\Yaml\Yaml;

/**
 * Write the setup of a site to the filesystem
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FilesystemSetupWriter implements SetupWriterInterface
{
    protected $dir;

    public function __construct($dir)
    {
        $this->dir = realpath($dir);
    }

    protected function getTemplate()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/../Resources/skeleton/filesystem_config.yml'));
    }

    /**
     * {@inheritDoc}
     */
    public function write(array $params)
    {
        @mkdir($this->dir, 0777, true);
        if (!is_writable($this->dir)) {
            throw new \RuntimeException("The directory '".$this->dir."' is not writable.");
        }
        $data = $this->getTemplate();

        $data['site']['info']['name'] = $params['name'];
        $data['site']['post']['info']['author'] = $params['author'];
        $data['site']['post']['info']['author_email'] = $params['author-email'];

        if (is_array($params['categories']) && count($params['categories']) > 0) {
            foreach ($params['categories'] as $k=>$v) {
                $data['filesystem']['categories'][$k] = array('name'=>$v);
            }
        }

        $path = $this->dir.'/config.yml';
        if (!@file_put_contents($path, Yaml::dump($data, 4))) {
            throw new \RuntimeException("Cannot write the setup to file '".$path."'.");
        }
    }

    public function read()
    {
        $path = $this->dir.'/config.yml';
        if (!is_readable($path)) {
            return array();
        }
        $data = array_merge($this->getTemplate(), Yaml::parse(file_get_contents($path)));
        $params = array();

        if (isset($data['site']['info']['name'])) {
            $params['name'] = $data['site']['info']['name'];
        }
        if (isset($data['site']['post']['info']['author'])) {
            $params['author'] = $data['site']['post']['info']['author'];
        }
        if (isset($data['site']['post']['info']['author_email'])) {
            if (!isset($params['author'])) {
                $params['author'] = '';
            } else {
                $params['author'] .= ' ';
            }
            $params['author'] .= '<'.$data['site']['post']['info']['author_email'].'>';
        }

        return $params;
    }
}
