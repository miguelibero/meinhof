<?php

namespace Meinhof\Command;

use Meinhof\Meinhof;
use Meinhof\Command\Helper\DialogHelper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class MeinhofCommand extends Command
{
    protected $meinhof;

    protected function getMeinhof()
    {
        return $this->meinhof;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        $this->meinhof = new Meinhof($dir, $input, $output);
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Meinhof\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}