<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InitCommand extends MeinhofCommand
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Creates a new site structure')
            ->setDefinition(array(
                 new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
                 new InputOption('site-name', 's', InputOption::VALUE_REQUIRED, 'The name of the site to create', 'Meinhof test blog'),
                 new InputOption('empty', 'e', InputOption::VALUE_NONE, 'Do not add initial content'),
                 new InputOption('pages', 'p', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'List of initial site pages'),
                 new InputOption('categories', 'c', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'List of initial site categories'),
                 new InputOption('update', 'u', InputOption::VALUE_NONE, 'Update the site after the init'),
            ))            
            ->setHelp(<<<EOT
<info>php meinhof.phar init</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->getMeinhof()->init();

        $dir = $input->getArgument('dir');
        $this->installComposer($dir, $output);

        if($input->getOption('update')){
            $update = new UpdateCommand();
            $update->execute($input, $output);
        }
    }

    protected function installComposer($dir, OutputInterface $output)
    {
        if(!file_exists($dir.'/composer.json')){
            return;
        }
        $write_output = function ($type, $buffer) use ($output) {
            if ('err' === $type) {
                $buffer = '<error>'.$buffer.'</error>';
            }
            $prefix = '<info>[composer]</info> ';
            $buffer = str_replace("\n","\n".$prefix, $buffer);
            $output->write($buffer);
        };

        $output->writeln('downloading composer...');
        $process = new Process('curl -s http://getcomposer.org/installer | php', $dir);
        $process->run($write_output);
        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        $output->writeln('downloading composer dependencies...');
        $process = new Process('php composer.phar install', $dir);
        $process->run($write_output);
        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }        
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Meinhof site configurator');

        $output->writeln(array(
            '',
            'This will generate a basic site configuration structure for you.',
            '',
            'Please answer some basic questions for the setup.',
            '',
        ));

        $sitename = $dialog->ask($output, $dialog->getQuestion('Site name',
            $input->getOption('site-name')), $input->getOption('site-name'));
        $input->setOption('site-name', $sitename);

        $empty = !$dialog->askConfirmation($output, $dialog->getQuestion('Do you want to add initial content', 'yes', '?'), true);

        $input->setOption('empty', $empty);

        if($empty){
            return;
        }

        $output->writeln(array(
            '',
            'Please enter the names of the initial pages of your site separated by commas.',
            '',
        ));

        $pages = $dialog->askForArray($output, $dialog->getQuestion('Pages',
            $input->getOption('pages')), $input->getOption('pages'));
        $input->setOption('pages', $pages);

        $output->writeln(array(
            '',
            'Please enter the names of the initial post categories of your site separated by commas.',
            '',
        ));

        $categories = $dialog->ask($output, $dialog->getQuestion('Categories',
            $input->getOption('categories')), $input->getOption('categories'));
        $input->setOption('categories', $categories);        

        $update = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to update the site after the init', 'yes', '?'), true);
        $input->setOption('update', $update);        
    }

}