<?php

declare(ticks = 1);

namespace Meinhof\Command;

use Meinhof\Meinhof;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Command that loads a webserver and listens to changes
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ServerCommand extends WatchCommand
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
                 new InputOption('address', 'a', InputOption::VALUE_REQUIRED, 'Address', '127.0.0.1'),
                 new InputOption('port', 'p', InputOption::VALUE_REQUIRED, 'The port to start the server', 8900)
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

        $signalHandler = function() use ($output) {
            exit();
        };

        pcntl_signal(SIGTERM, $signalHandler);
        pcntl_signal(SIGINT, $signalHandler);

        $meinhof = $this->getMeinhof();
        $meinhof->update();

        $documentRoot = $meinhof->getParameter('filesystem.paths.web');

        $phpExecutableFinder = new PhpExecutableFinder();

        if (false === $binary = $phpExecutableFinder->find()) {
            $output->writeln('<error>Unable to find PHP binary to run server</error>');
            return;
        }

        $this->launchServer($input, $output, $documentRoot, $binary);

        $this->watch($output);
    }

    protected function launchServer(InputInterface $input, OutputInterface $output, $documentRoot, $binary)
    {
        $output->writeln(sprintf(
            "Server running on <info>http://%s:%s</info> with document root <info>%s</info>",
            $input->getOption('address'),
            $input->getOption('port'),
            $documentRoot
        ));

        pcntl_fork();

        $processBuilder = new ProcessBuilder(array(
            $binary,
            '-S',
            $input->getOption('address').':' . $input->getOption('port'),
            '--docroot',
            $documentRoot
        ));

        $process = $processBuilder->getProcess();
        $process->start();

        if (!$process->isRunning()) {
            $output->writeln('<error>Unable to start the server process</error>');
            return 1;
        }

        while ($process->isRunning()) {
            // block this thread
        }
    }
}
