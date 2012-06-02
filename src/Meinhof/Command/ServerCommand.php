<?php

namespace Meinhof\Command;

use Meinhof\Meinhof;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that loads a webserver and listens to changes
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ServerCommand extends MeinhofCommand
{
    /**
     * @{inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('server')
            ->setDescription('Starts a local server and watches for changes')
            ->setDefinition(array(
                 new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
                 new InputOption('port', 'p', InputOption::VALUE_REQUIRED, 'The port to start the server'),
                 new InputOption('base-url', 'u', InputOption::VALUE_OPTIONAL, 'The base url of the site'),
            ))
        ;
    }

    /**
     * @{inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (strnatcmp(phpversion(),'5.4') < 0) {
            throw new \RuntimeException("The development server only works in php versions 5.4 and up.");
        }
    }
}
