<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Command\Helper\DialogHelper;
use Meinhof\Generator\GeneratorInterface;

/**
 * This action creates a new site configuration structure from a generator.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class InitSiteAction extends OutputAction
{
    protected $generator;
    protected $input;
    protected $output;

    /**
     * Constructor.
     *
     * @param GeneratorInterface $generator the meinhof generator
     * @param InputInterface     $input     the command line input to read the parameters
     * @param OutputInterface    $output    the command line output to write log
     */
    public function __construct(GeneratorInterface $generator, InputInterface $input, OutputInterface $output=null)
    {
        $this->generator = $generator;
        $this->output = $output;
        $this->input = $input;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Checks if a given path is a directory with files in it
     *
     * @param string $dir path to the directory
     *
     * @return Boolean if it is a full directory
     */
    public function isFullDirectory($dir)
    {
        if (!file_exists($dir)) {
            return false;
        }
        if (!is_readable($dir)) {
            throw new \InvalidArgumentException("File '${dir}' is not readable.");
        }
        if (!is_dir($dir)) {
            return false;
        }
        $dh = opendir($dir);
        if (!$dh) {
            throw new \RuntimeException("Could not read '${dir}'.");
        }
        $i = 0;
        $invalid_files = array('.', '..', 'meinhof.phar');
        while (($file = readdir($dh)) !== false) {
            if (!in_array($file, $invalid_files)) {
                $i++;
            }
        }
        closedir($dh);

        return $i>0;
    }

    /**
     * Creates the site structure.
     */
    public function take()
    {
        $params = $this->input->getOptions();
        $dir = $this->input->getArgument('dir');

        if (!file_exists($dir)) {
            if (!@mkdir($dir)) {
                throw new \RuntimeException("Could not create directory '${dir}'.");
            }
        }
        if (is_file($dir)) {
            throw new \RuntimeException("'${dir}' is a file.");
        }
        if (!is_writable($dir)) {
            throw new \RuntimeException("Directory '${dir}' is not writable.");
        }

        if ($this->isFullDirectory($dir)) {
            if (!$this->input->isInteractive()) {
                throw new \RuntimeException("Directory ${dir} already exists and has files.");
            }
            $dialog = new DialogHelper();
            $force = $dialog->askConfirmation($this->output, $dialog->getQuestion('Directory has files, do you want to init anyway', 'no', '?'), false);
            if (!$force) {
                return;
            }
        }

        $this->writeOutputLine("initiating site configuration...", 2);

        $this->generator->generate($params, $dir);

        $this->writeOutputLine("done", 2);
    }
}
