<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meinhof\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\StreamingEngineInterface;
use Symfony\Component\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

/**
 * DelegatingEngine selects an engine for a given template.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class EventDispatchingEngine implements EngineInterface, StreamingEngineInterface
{
    protected $engine;
    protected $dispatcher;

    /**
     * @inheritDoc
     */
    public function __construct(EngineInterface $engine, EventDispatcherInterface $dispatcher)
    {
        $this->engine = $engine;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function render($name, array $parameters = array())
    {
        $this->dispatcher->dispatch('templating.render');
        return $this->engine->render($name, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function stream($name, array $parameters = array())
    {
        if (!$this->engine instanceof StreamingEngineInterface) {
            throw new \LogicException(sprintf('Template "%s" cannot be streamed as the engine supporting it does not implement StreamingEngineInterface.', $name));
        }
        $this->dispatcher->dispatch('templating.stream');
        $this->engine->stream($name, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function exists($name)
    {
        return $this->engine->exists($name);
    }

    /**
     * @inheritDoc
     */
    public function supports($name)
    {
        return $this->engine->supports($name);
    }
}
