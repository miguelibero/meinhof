<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('about')
            ->setDescription('Short information about Meinhof')
            ->setHelp(<<<EOT
<info>php meinhof.phar about</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(<<<EOT
<info>Meinhof - PHP static blog generator</info>
<comment>Meinhof is a static blog generator similar to jekyll
See http://github.com/miguelibero/meinhof for more information.</comment>

http://github.com/miguelibero/meinhof
EOT
        );

    }
}
