<?php

namespace Smolblog\App;

use Psr\Container\ContainerInterface;
use Smolblog\Core\{Connector, Importer};

/**
 * The core app class.
 */
class Smolblog {
	/**
	 * Dependency Injection container
	 *
	 * @var Container\Container
	 */
	public readonly Container\Container $container;

	/**
	 * Event dispatcher
	 *
	 * @var Hooks\EventDispatcher
	 */
	public readonly Hooks\EventDispatcher $events;

	/**
	 * Command bus
	 *
	 * @var CommandBus
	 */
	public readonly CommandBus $commands;

	/**
	 * Environment information
	 *
	 * @var Environment
	 */
	public readonly Environment $env;

	/**
	 * Array of Plugins that are currently active
	 *
	 * @var string[]
	 */
	private array $plugins = [];

	/**
	 * Construct a new App. Requires an EndpointRegistrar and a loaded Environment object. Loads the
	 * dependency injection container (`$app->container`) with core classes and dependencies.
	 *
	 * Once this object is constructed, it is the responsibility of the bootstrapper to add any further
	 * dependencies to the container before calling `startup()`.
	 *
	 * @param Environment $withEnvironment Environment information.
	 * @param string[]    $pluginClasses   Plugin classes to load.
	 */
	public function __construct(
		Environment $withEnvironment,
		array $pluginClasses
	) {
		$this->env = $withEnvironment;
		$this->plugins = $pluginClasses;

		$this->container = new Container\Container();
		$this->events = new Hooks\EventDispatcher();

		$this->loadContainerWithCoreClasses();

		$this->commands = new CommandBus(
			map: $this->createCommandMap(),
			container: $this->container,
		);

		foreach ($this->plugins as $plugin) {
			$plugin::setup(app: $this);
		}
	}

	/**
	 * Start the app!
	 *
	 * @return void
	 */
	public function startup(): void {
		// Register endpoints with external system.
		$coreEndpoints = [
			Connector\ConnectCallback::class,
			Connector\ConnectInit::class,
			Connector\UserConnections::class,
			Plugin\InstalledPlugins::class,
		];
		$allEndpoints = $this->events->dispatch(new Hooks\CollectingEndpoints($coreEndpoints))->endpoints;
		$endpointRegistrar = $this->container->get(Endpoint\EndpointRegistrar::class);
		foreach ($allEndpoints as $endpoint) {
			$endpointRegistrar->register(class: $endpoint);
		}

		// Collect and register Connectors.
		$allConnectors = $this->events->dispatch(new Hooks\CollectingConnectors([]))->connectors;
		$connectorRegistrar = $this->container->get(Registrars\ConnectorRegistrar::class);
		foreach ($allConnectors as $slug => $connector) {
			$connectorRegistrar->register(key: $slug, class: $connector);
		}

		// Collect and register Importers.
		$allImporters = $this->events->dispatch(new Hooks\CollectingImporters([]))->importers;
		$importerRegistrar = $this->container->get(Registrars\ImporterRegistrar::class);
		foreach ($allImporters as $slug => $importer) {
			$importerRegistrar->register(key: $slug, class: $importer);
		}

		// We're done with our part; fire the event!
		$this->events->dispatch(new Hooks\Startup($this));
	}

	/**
	 * Load classes from this library into the DI container.
	 *
	 * @return void
	 */
	private function loadContainerWithCoreClasses(): void {
		$this->container->addShared(Environment::class, fn() => $this->env);
		$this->container->addShared(Hooks\EventDispatcher::class, fn() => $this->events);
		$this->container->addShared(CommandBus::class, fn() => $this->commands);

		$this->container->add(Connector\ConnectorRegistrar::class);
		$this->container->addShared(Registrars\ConnectorRegistrar::class)->addArgument(ContainerInterface::class);
		$this->container->setImplementation(
			interface: Connector\ConnectorRegistrar::class,
			class: Registrars\ConnectorRegistrar::class
		);

		$this->container->add(Connector\AuthRequestInitializer::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Connector\AuthRequestStateWriter::class);
		$this->container->add(Connector\ConnectInit::class)->
			addArgument(Environment::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(CommandBus::class);

		$this->container->add(Connector\AuthRequestFinalizer::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Connector\AuthRequestStateReader::class)->
			addArgument(Connector\ConnectionWriter::class)->
			addArgument(CommandBus::class);
		$this->container->add(Connector\ConnectCallback::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Connector\AuthRequestStateReader::class)->
			addArgument(CommandBus::class);

		$this->container->add(Connector\ChannelRefresher::class)->
			addArgument(Connector\ConnectionReader::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Connector\ChannelReader::class)->
			addArgument(Connector\ChannelWriter::class);

		$this->container->add(Connector\UserConnections::class)->
			addArgument(Connector\ConnectionReader::class)->
			addArgument(Connector\ChannelReader::class);

		$this->container->add(Connector\RefreshConnectionToken::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Connector\ConnectionWriter::class);

		$this->container->addShared(Importer\ImporterRegistrar::class);
		$this->container->addShared(Registrars\ImporterRegistrar::class)->addArgument(ContainerInterface::class);
		$this->container->setImplementation(
			interface: Importer\ImporterRegistrar::class,
			class: Registrars\ImporterRegistrar::class
		);

		$this->container->add(Importer\ImportStarter::class)->
			addArgument(Connector\ChannelReader::class)->
			addArgument(Connector\ConnectionReader::class)->
			addArgument(Connector\RefreshConnectionToken::class)->
			addArgument(Importer\ImporterRegistrar::class)->
			addArgument(Post\PostWriter::class)->
			addArgument(CommandBus::class);

		$this->container->add(Importer\RemoveAlreadyImported::class)->
			addArgument(Post\PostReader::class);

		$this->container->addShared(Container\Container::class, fn() => $this->container);
		$this->container->setImplementation(
			interface: ContainerInterface::class,
			class: Container\Container::class
		);

		$this->container->add(
			Plugin\InstalledPlugins::class,
			fn() => new Plugin\InstalledPlugins(
				installedPlugins: $this->plugins,
			)
		);
	}

	/**
	 * Create a map of command classes to handlers.
	 *
	 * @return array
	 */
	private function createCommandMap(): array {
		$map = [
			Connector\Commands\BeginAuthRequest::class => Connector\Services\AuthRequestInitializer::class,
			Connector\Commands\FinishAuthRequest::class => Connector\Services\AuthRequestFinalizer::class,
			Connector\Commands\RefreshChannels::class => Connector\Services\ChannelRefresher::class,
			Importer\PullFromChannel::class => Importer\ImporterStarter::class,
		];
		return $map;
	}
}
