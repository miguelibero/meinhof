<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends MeinhofCommand
{
    protected function configure()
    {
        $this
            ->setName('watch')
            ->setDescription('Watches for changes in and updates the site')
            ->setDefinition(array(
                 new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
            ))            
            ->setHelp(<<<EOT
<info>php meinhof.phar watch</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}