<?php

namespace Meinhof\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * The methods of this class are called
 * by the composer package manager
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ScriptHandler
{
    public static function onInstall($event)
    {
        self::executeUpdate($event);        
    }

    public static function onUpdate($event)
    {
        self::executeUpdate($event);
    }

    protected static function executeUpdate($event)
    {
        $options = self::getOptions($event);
        $siteDir = $options['meinhof-site-dir'];

        if (!is_dir($siteDir)) {
            echo 'The meinhof-site-dir ('.$siteDir.') specified in composer.json was not found in '.getcwd().PHP_EOL;
            return;
        }

        static::executeCommand($event, $siteDir, 'update');        
    }

    protected static function executeCommand($event, $siteDir, $cmd)
    {
        $phpFinder = new PhpExecutableFinder;
        $php = escapeshellarg($phpFinder->find());
        $meinhof = escapeshellarg($siteDir.'/bin/meinhof');
        if ($event->getIO()->isDecorated()) {
            $meinhof.= ' --ansi';
        }

        $process = new Process($php.' '.$meinhof.' '.$cmd);
        $process->run(function ($type, $buffer) { echo $buffer; });
    }

    protected static function getOptions($event)
    {
        $options = array_merge(array(
            'meinhof-site-dir' => '.',
        ), $event->getComposer()->getPackage()->getExtra());
        return $options;
    }    

}
