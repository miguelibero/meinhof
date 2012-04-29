<?php

namespace Meinhof\Command;

use Meinhof\Meinhof;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generates the html files based on a configuration directory')
            ->setDefinition(array(
                new InputArgument('path', InputArgument::REQUIRED, 'base path of the site configuration'),
            ))            
            ->setHelp(<<<EOT
<info>php meinhof.phar generate</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = realpath($input->getArgument('path'));

        $meinhof = new Meinhof($dir);
        $meinhof->generate();
    }
}