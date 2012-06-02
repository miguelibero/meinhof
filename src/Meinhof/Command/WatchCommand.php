<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that watches for changes and calls update
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class WatchCommand extends MeinhofCommand
{

    /**
     * @{inheritDoc}
     */    
    protected function configure()
    {
        $this
            ->setName('watch')
            ->setDescription('Watches for changes in and updates the site')
            ->setDefinition(array(
                 new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
            ))
        ;
    }

    /**
     * @{inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
