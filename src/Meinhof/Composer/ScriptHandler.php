<?php

namespace Meinhof\Composer;

use Meinhof\Command\UpdateCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\FormatterHelper;

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
        try {
            self::executeUpdate($event);
        } catch (\Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    public static function onUpdate($event)
    {
        try {
            self::executeUpdate($event);
        } catch (\Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    protected static function executeUpdate($event)
    {
        $options = self::getOptions($event);
        $siteDir = $options['meinhof-site-dir'];

        $command = new UpdateCommand();
        self::executeCommand($command, $siteDir);
    }

    protected static function executeCommand(Command $command, $siteDir)
    {
        if (!is_dir($siteDir)) {
            throw new \RuntimeException('The meinhof-site-dir ('.$siteDir.') specified in composer.json was not found in '.getcwd());
        }

        $helpers = new HelperSet(array(
            new FormatterHelper(),
            new DialogHelper(),
        ));

        $command->setHelperSet($helpers);
        $input = new ArrayInput(array('dir'=>$siteDir));
        $output = new ConsoleOutput();
        $command->run($input, $output);
    }

    protected static function getOptions($event)
    {
        $options = array_merge(array(
            'meinhof-site-dir' => '.',
        ), $event->getComposer()->getPackage()->getExtra());

        return $options;
    }

}
