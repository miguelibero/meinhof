<?php

namespace Meinhof\Command\Helper;

use Symfony\Component\Console\Helper\DialogHelper as BaseDialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Some utility functions for the commands.
 * This was copied from the symfony source.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class DialogHelper extends BaseDialogHelper
{
    /**
     * Reurns a closure that prints a success or failure
     * and the failure error is added to a list of errors.
     *
     * @param OutputInterface $output the command line output
     * @param array &$errors a list of errors where the error will be added
     *
     * @return function the runner function
     */
    public function getRunner(OutputInterface $output, &$errors)
    {
        $runner = function ($err) use ($output, &$errors) {
            if ($err) {
                $output->writeln('<fg=red>FAILED</>');
                $errors = array_merge($errors, $err);
            } else {
                $output->writeln('<info>OK</info>');
            }
        };

        return $runner;
    }

    /**
     * Writes a line to the output representing the title of a section
     *
     * @param OutputInterface $output the command line output
     * @param string          $text   the title text
     * @param string          $style  the title style
     */
    public function writeSection(OutputInterface $output, $text, $style = 'bg=blue;fg=white')
    {
        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock($text, $style, true),
            '',
        ));
    }

    /**
     * Formats a question to be printed in the command line
     *
     * @param string $question the question text
     * @param string $default  the default answer
     * @param string $sep      the separator
     *
     * @return string the formatted question
     */
    public function getQuestion($question, $default, $sep = ':')
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }

    /**
     * Asks for a list of values, the response is split to return an array
     *
     * @param OutputInterface $output   the command line output
     * @param string          $question the question to ask
     * @param string          $default  the default answer
     *
     * @return array the array of resposne values
     */
    public function askForArray(OutputInterface $output, $question, $default)
    {
        $result = $this->ask($output, $question, $default);
        if (is_string($result)) {
            $result = explode(',', $result);
        }
        if (!is_array($result)) {
            $result = array();
        }
        $result = array_map('trim', $result);

        return $result;
    }
}
