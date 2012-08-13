<?php

namespace Meinhof\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Meinhof\Command;
use Meinhof\Meinhof;

/**
 * The console application that handles the commands
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class Application extends BaseApplication
{
    protected $meinhof;

    public function __construct()
    {
        parent::__construct('Meinhof', Meinhof::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $styles['highlight'] = new OutputFormatterStyle('red');
            $styles['warning'] = new OutputFormatterStyle('black', 'yellow');
            $formatter = new OutputFormatter(null, $styles);
            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }
        $this->registerCommands();

        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    protected function registerCommands()
    {
        $this->add(new Command\AboutCommand());
        $this->add(new Command\SetupCommand());
        $this->add(new Command\UpdateCommand());
        $this->add(new Command\WatchCommand());
        $this->add(new Command\ServerCommand());
        $this->add(new Command\ServicesCommand());
    }

}
