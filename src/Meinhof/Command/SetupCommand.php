<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command will setup a base site configuration for you.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class SetupCommand extends MeinhofCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Creates the site configuration')
            ->setDefinition(array(
                 new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
                 new InputOption('author', 'a', InputOption::VALUE_REQUIRED, 'The name of the author', 'Joe Schmoe <joe@schmoe.com>'),
                 new InputOption('name', 's', InputOption::VALUE_REQUIRED, 'The name of the site', 'Meinhof site'),
                 new InputOption('categories', 'c', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'List of site categories'),
                 new InputOption('update', 'u', InputOption::VALUE_NONE, 'Update the site after the setup'),
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $writer = $this->getMeinhof()->get('setup_writer');
        foreach ($writer->read() as $k=>$v) {
            $input->setOption($k, $v);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getMeinhof()->setup();

        if ($input->getOption('update')) {
            $update = new UpdateCommand();
            $update->initialize($input, $output);
            $update->execute($input, $output);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Meinhof site setup');

        $output->writeln(array(
            '',
            'This will generate a basic site configuration structure for you.',
            '',
            'Please answer some basic questions for the setup.',
            '',
        ));

        $author = $dialog->ask($output, $dialog->getQuestion('Author name',
            $input->getOption('author')), $input->getOption('author'));
        $input->setOption('author', $author);

        $name = $dialog->ask($output, $dialog->getQuestion('Site name',
            $input->getOption('name')), $input->getOption('name'));
        $input->setOption('name', $name);

        $output->writeln(array(
            '',
            'Please enter the names of the post categories of your site separated by commas.',
            '',
        ));

        $categories = $dialog->askForArray($output, $dialog->getQuestion('Categories',
            $input->getOption('categories')), $input->getOption('categories'));
        $input->setOption('categories', $categories);

        $update = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to update the site after the setup', 'yes', '?'), true);
        $input->setOption('update', $update);
    }

}
