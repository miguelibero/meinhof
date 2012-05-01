<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

abstract class OutputAction implements ActionInterface
{
    /**
     * @return OutputInterface
     */
    abstract protected function getOutput();

    protected function shouldWriteOutput($min=0, $max=null)
    {
        $out = $this->getOutput();
        if(!$out instanceof OutputInterface){
            return false;
        }
        $v = $out->getVerbosity();
        return !($v<$min || ($max!==null && $v>$max));
    }

    protected function writeOutputLine($msg, $min=0, $max=null)
    {
        if(!$this->shouldWriteOutput($min, $max)){
            return;
        }
        return $this->getOutput()->writeln($msg);
    }

    protected function getOutputVerbosity()
    {
        $out = $this->getOutput();
        if($out instanceof OutputInterface){
            return $out->getVerbosity();
        }else{
            return 0;
        }
    }

    protected function writeOutput($msg, $min=0, $max=null)
    {
        if(!$this->shouldWriteOutput($min, $max)){
            return;
        }
        return $this->getOutput()->write($msg);
    }

}