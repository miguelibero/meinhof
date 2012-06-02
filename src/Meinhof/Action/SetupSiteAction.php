<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Setup\SetupWriterInterface;

/**
 * This action creates a new site configuration structure from a generator.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class SetupSiteAction extends OutputAction
{
    protected $writer;
    protected $input;
    protected $output;

    /**
     * @param SetupWriterInterface  $writer    the setup writer
     * @param InputInterface        $input     the command line input to read the parameters
     * @param OutputInterface       $output    the command line output to write log
     */
    public function __construct(SetupWriterInterface $writer, InputInterface $input, OutputInterface $output=null)
    {
        $this->writer = $writer;
        $this->output = $output;
        $this->input = $input;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Creates the site structure.
     */
    public function take()
    {
        $params = array_merge(array(
            'author'        => null,
            'categories'    => null
        ), $this->input->getOptions());

        $params = $this->fixAuthorSetupParameters($params);
        $params = $this->fixCategoriesSetupParameters($params);

        $this->writeOutputLine("writing site configuration...", 2);

        $this->writer->write($params);

        $this->writeOutputLine("done", 2);
    }

    protected function fixAuthorSetupParameters(array $params)
    {
        if(!isset($params['author'])){
            $params['author'] = null;
        }
        if(preg_match('/(.*) <(.+)>/', $params['author'], $m)){
            $params['author'] = $m[1];
            $params['author-email'] = $m[2];
        }else{
            $params['author-email'] = null;
        }
        return $params;        
    }

    protected function fixCategoriesSetupParameters(array $params)
    {
        if(!isset($params['categories']) || !is_array($params['categories'])){
            return $params;
        }
        $categories = array();
        foreach($params['categories'] as $name){
            $slug = preg_replace('/[^a-z0-9]+/',' ', mb_strtolower($name));
            $slug = str_replace(' ','-', trim($slug));
            $categories[$slug] = $name;
        }
        $params['categories'] = $categories;
        return $params;
    }
}
