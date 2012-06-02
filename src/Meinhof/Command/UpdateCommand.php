<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that generates the update site pages
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UpdateCommand extends MeinhofCommand
{

    /**
     * @{inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Updates the site structure reading the site configuration')
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
        $this->getMeinhof()->update();
    }
}
