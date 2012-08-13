<?php

namespace Meinhof\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * A console command for retrieving information about services.
 * Copied from the Symfony FrameworkBundle ContainerDebugCommand
 *
 * @author Ryan Weaver <ryan@thatsquality.com>
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ServicesCommand extends MeinhofCommand
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->meinhof->get('service_container');
    }

    /**
     * @see ContainerAwareInterface::setContainer()
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }    

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('services')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'A service name (foo)'),
                new InputOption('show-private', null, InputOption::VALUE_NONE, 'Use to show public *and* private services'),
                new InputArgument('dir', InputArgument::OPTIONAL, 'base directory of the site configuration', '.'),
            ))
            ->setDescription('Displays current services for an application')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command displays all configured <comment>public</comment> services:

  <info>php %command.full_name%</info>

To get specific information about a service, specify its name:

  <info>php %command.full_name% validator</info>

By default, private services are hidden. You can display all services by
using the --show-private flag:

  <info>php %command.full_name% --show-private</info>
EOF
            )
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $dir = $input->getArgument('dir');

        $this->containerBuilder = $this->getContainerBuilder($dir);
        $serviceIds = $this->containerBuilder->getServiceIds();

        // sort so that it reads like an index of services
        asort($serviceIds);

        if ($name) {
            $this->outputService($output, $name);
        } else {
            $this->outputServices($output, $serviceIds, $input->getOption('show-private'));
        }
    }

    protected function outputServices(OutputInterface $output, $serviceIds, $showPrivate = false)
    {
        // set the label to specify public or public+private
        if ($showPrivate) {
            $label = '<comment>Public</comment> and <comment>private</comment> services';
        } else {
            $label = '<comment>Public</comment> services';
        }

        $output->writeln($this->getHelper('formatter')->formatSection('container', $label));

        // loop through to get space needed and filter private services
        $maxName = 4;
        $maxScope = 6;
        foreach ($serviceIds as $key => $serviceId) {
            $definition = $this->resolveServiceDefinition($serviceId);

            if ($definition instanceof Definition) {
                // filter out private services unless shown explicitly
                if (!$showPrivate && !$definition->isPublic()) {
                    unset($serviceIds[$key]);
                    continue;
                }

                if (strlen($definition->getScope()) > $maxScope) {
                    $maxScope = strlen($definition->getScope());
                }
            }

            if (strlen($serviceId) > $maxName) {
                $maxName = strlen($serviceId);
            }
        }
        $format  = '%-'.$maxName.'s %-'.$maxScope.'s %s';

        // the title field needs extra space to make up for comment tags
        $format1  = '%-'.($maxName + 19).'s %-'.($maxScope + 19).'s %s';
        $output->writeln(sprintf($format1, '<comment>Service Id</comment>', '<comment>Scope</comment>', '<comment>Class Name</comment>'));

        foreach ($serviceIds as $serviceId) {
            $definition = $this->resolveServiceDefinition($serviceId);

            if ($definition instanceof Definition) {
                $output->writeln(sprintf($format, $serviceId, $definition->getScope(), $definition->getClass()));
            } elseif ($definition instanceof Alias) {
                $alias = $definition;
                $output->writeln(sprintf($format, $serviceId, 'n/a', sprintf('<comment>alias for</comment> <info>%s</info>', (string) $alias)));
            } else {
                // we have no information (happens with "service_container")
                $service = $definition;
                $output->writeln(sprintf($format, $serviceId, '', get_class($service)));
            }
        }
    }

    /**
     * Renders detailed service information about one service
     */
    protected function outputService(OutputInterface $output, $serviceId)
    {
        $definition = $this->resolveServiceDefinition($serviceId);

        $label = sprintf('Information for service <info>%s</info>', $serviceId);
        $output->writeln($this->getHelper('formatter')->formatSection('container', $label));
        $output->writeln('');

        if ($definition instanceof Definition) {
            $output->writeln(sprintf('<comment>Service Id</comment>       %s', $serviceId));
            $output->writeln(sprintf('<comment>Class</comment>            %s', $definition->getClass()));

            $tags = $definition->getTags() ? implode(', ', array_keys($definition->getTags())) : '-';
            $output->writeln(sprintf('<comment>Tags</comment>             %s', $tags));

            $output->writeln(sprintf('<comment>Scope</comment>            %s', $definition->getScope()));

            $public = $definition->isPublic() ? 'yes' : 'no';
            $output->writeln(sprintf('<comment>Public</comment>           %s', $public));

            $synthetic = $definition->isSynthetic() ? 'yes' : 'no';
            $output->writeln(sprintf('<comment>Synthetic</comment>        %s', $synthetic));

            $file = $definition->getFile() ? $definition->getFile() : '-';
            $output->writeln(sprintf('<comment>Required File</comment>    %s', $file));
        } elseif ($definition instanceof Alias) {
            $alias = $definition;
            $output->writeln(sprintf('This service is an alias for the service <info>%s</info>', (string) $alias));
        } else {
            // edge case (but true for "service_container", all we have is the service itself
            $service = $definition;
            $output->writeln(sprintf('<comment>Service Id</comment>   %s', $serviceId));
            $output->writeln(sprintf('<comment>Class</comment>        %s', get_class($service)));
        }
    }

    /**
     * Loads the ContainerBuilder from the cache.
     *
     * @return ContainerBuilder
     */
    protected function getContainerBuilder($dir)
    {
        return $this->meinhof->buildContainer($dir);
    }

    /**
     * Given an array of service IDs, this returns the array of corresponding
     * Definition and Alias objects that those ids represent.
     *
     * @param string $serviceId The service id to resolve
     *
     * @return \Symfony\Component\DependencyInjection\Definition|\Symfony\Component\DependencyInjection\Alias
     */
    private function resolveServiceDefinition($serviceId)
    {
        if ($this->containerBuilder->hasDefinition($serviceId)) {
            return $this->containerBuilder->getDefinition($serviceId);
        }

        // Some service IDs don't have a Definition, they're simply an Alias
        if ($this->containerBuilder->hasAlias($serviceId)) {
            return $this->containerBuilder->getAlias($serviceId);
        }

        // the service has been injected in some special way, just return the service
        return $this->containerBuilder->get($serviceId);
    }
}
