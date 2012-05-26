<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends MeinhofCommand
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Updates the site structure reading the site configuration')
            ->setDefinition(array(
                new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
            ))
            ->setHelp(<<<EOT
<info>php meinhof.phar update</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->getMeinhof()->update();
    }
}
