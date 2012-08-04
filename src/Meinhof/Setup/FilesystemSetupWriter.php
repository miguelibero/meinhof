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
        $this->dir = $dir;
    }

    protected function loadTemplate($path)
    {
        if(!is_file($path) || !is_readable($path)){
            return array();
        }
        return Yaml::parse(file_get_contents($path));
    }

    protected function saveTemplate($path, array $data)
    {
        try{
            return @file_put_contents($path, Yaml::dump($data, 4));
        }catch(\Exception $e){
            return false;
        }
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
        $path = $this->dir.'/config.yml';
        $data = array_merge($this->loadSkeleton(), $this->loadTemplate($path));

        if(isset($params['name'])){
            $data['site']['info']['name'] = $params['name'];
        }
        if(isset($params['author'])){
            $data['site']['post']['info']['author'] = $params['author'];
        }
        if(isset($params['author-email'])){
            $data['site']['post']['info']['author_email'] = $params['author-email'];
        }

        if (isset($params['categories']) && is_array($params['categories']) && count($params['categories']) > 0) {
            foreach ($params['categories'] as $k=>$v) {
                $data['filesystem']['categories'][$k] = array('name'=>$v);
            }
        }

        if (!$this->saveTemplate($path, $data)) {
            throw new \RuntimeException("Cannot write the setup to file '".$path."'.");
        }
    }

    protected function loadSkeleton()
    {
        $path = __DIR__.'/../Resources/skeleton/filesystem_config.yml';
        return $this->loadTemplate($path);
    }

    public function read()
    {
        $path = $this->dir.'/config.yml';
        if (!is_readable($path)) {
            return array();
        }
        $data = array_merge($this->loadSkeleton(), $this->loadTemplate($path));
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
