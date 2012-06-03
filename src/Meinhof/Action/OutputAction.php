<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Defines an action that can write to an output interface
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
abstract class OutputAction implements ActionInterface
{
    /**
     * Classes that inherit this one should implement this method
     * returning a command line output.
     *
     * @return OutputInterface
     */
    abstract protected function getOutput();

    /**
     * Checks if the output verbositiy is between two given levels
     *
     * @param integer $min minimum verbosity level
     * @param integer $max maximum verbosity level or null for no maximum
     *
     * @return boolean if output should be written
     */
    protected function shouldWriteOutput($min=0, $max=null)
    {
        $out = $this->getOutput();
        if (!$out instanceof OutputInterface) {
            return false;
        }
        $v = $out->getVerbosity();

        return !($v<$min || ($max!==null && $v>$max));
    }

    /**
     * Prints a text line if the output verbositiy is between two given levels
     *
     * @param string  $msg the text to print
     * @param integer $min minimum verbosity level
     * @param integer $max maximum verbosity level or null for no maximum
     */
    protected function writeOutputLine($msg, $min=0, $max=null)
    {
        if (!$this->shouldWriteOutput($min, $max)) {
            return;
        }

        $this->getOutput()->writeln($msg);
    }

    /**
     * Returns the output verbosity level.
     *
     * @return integer verbosity
     */
    protected function getOutputVerbosity()
    {
        $out = $this->getOutput();
        if ($out instanceof OutputInterface) {
            return $out->getVerbosity();
        } else {
            return 0;
        }
    }

    /**
     * Prints a text if the output verbositiy is between two given levels
     *
     * @param string  $msg the text to print
     * @param integer $min minimum verbosity level
     * @param integer $max maximum verbosity level or null for no maximum
     */
    protected function writeOutput($msg, $min=0, $max=null)
    {
        if (!$this->shouldWriteOutput($min, $max)) {
            return;
        }

        return $this->getOutput()->write($msg);
    }

}
