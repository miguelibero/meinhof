<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

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
        $meinhof = $this->getMeinhof();
        if (!$meinhof->isSiteConfigured()) {
            $output->writeln('looks like your site needs to be set up first...');
            $this->runSetup($input, $output);
            $meinhof->reload();
        }
        $meinhof->update();
    }

    protected function runSetup(InputInterface $input, OutputInterface $output)
    {
        $command = new SetupCommand();
        if ($helper = $this->getHelperSet()) {
            $command->setHelperSet($helper);
        }
        $input = new ArrayInput(array(
            'dir'       => $input->getArgument('dir'),
            '--update'  => false,
        ));
        $command->run($input, $output);
    }
}
