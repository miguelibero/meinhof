<?php

namespace Meinhof\Action;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Finder\Finder;

/**
 * This action loads all translation resources in a path into the translator
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class LoadFilesystemTranslationResourcesAction implements ActionInterface
{
    protected $translator;
    protected $path;
    protected $regex = "/(?P<domain>.+)\.(?P<locale>[^.]+)\.(?P<format>[^.]+)/";

    public function __construct(TranslatorInterface $translator, $path)
    {
        $this->translator = $translator;
        $this->path = $path;
    }

    public function take()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->path);

        foreach ($finder as $file) {
            $name = $file->getFilename();
            if (preg_match($this->regex, $name, $m)) {
                $path = $file->getRealPath();
                $this->translator->addResource($m['format'], $path, $m['locale'], $m['domain']);
            }
        }
    }
}
