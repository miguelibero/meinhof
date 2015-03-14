<?php

namespace Meinhof\Helper;

use Symfony\Component\Finder\Finder;

class FileWatcherHelper
{
    private $watchDir;

    private $sleepPeriod;

    public function __construct($watchDir, $sleepPeriod = 1)
    {
        $this->watchDir = $watchDir;
        $this->sleepPeriod = $sleepPeriod;
    }

    public function watch(callable $successCallback, callable $errorCallback)
    {
        $cache = sys_get_temp_dir() . '/meinhof_watch_' . substr(sha1($this->watchDir), 0, 7);

        $previously = unserialize(file_get_contents($cache));

        if (!is_array($previously)) {
            $previously = array();
        }

        $error = '';

        while (true) {
            try {
                $files = Finder::create()->files()->in($this->watchDir);

                foreach ($files as $file) {
                    if ($this->isFileChanged($file, $previously)) {
                        $successCallback($file);
                    }
                }

                file_put_contents($cache, serialize($previously));
                $error = '';
            } catch (\Exception $e) {
                $errorCallback($e);
            }

            clearstatcache();
            sleep($this->sleepPeriod);
        }
    }

    private function isFileChanged(\SplFileInfo $file, &$previously)
    {
        $name = $file->getFilename();
        $mtime = $file->getMTime();

        if (isset($previously[$name])) {
            $changed = $previously[$name]['mtime'] != $mtime;
        } else {
            $changed = true;
        }

        $previously[$name] = array('mtime' => $mtime);

        return $changed;
    }
}
