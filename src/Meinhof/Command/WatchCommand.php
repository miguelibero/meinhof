<?php

namespace Meinhof\Command;

use Meinhof\Helper\FileWatcherHelper;
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
        $this->watch($output);
    }

    protected function watch($output)
    {
        $meinhof = $this->getMeinhof();
        $meinhof->update();

        $watchDir = $meinhof->getParameter('filesystem.paths.posts');

        $output->writeln('Watching for changes in <info>' . $watchDir. '</info>');

        $watcherHelper = new FileWatcherHelper($watchDir);

        $watcherHelper->watch(function(\SplFileInfo $file) use ($meinhof, $output) {
            $output->writeln('Dumping <info>' . $file->getFilename(). '</info>');

            $meinhof->update();
        }, function(\Exception $e) use ($output) {
            $output->writeln('Error dumping content: <error>' . $e->getMessage(). '</error>');
        });
    }
}
