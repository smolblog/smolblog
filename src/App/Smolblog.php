<?php

namespace Smolblog\App;

use Psr\Container\ContainerInterface;
use Smolblog\Core\{Events, Plugin, Connector, Importer};

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
	 * @var Events\EventDispatcher
	 */
	public readonly Events\EventDispatcher $events;

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
		$this->events = new Events\EventDispatcher();

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
		$allEndpoints = $this->events->dispatch(new Events\CollectingEndpoints($coreEndpoints))->endpoints;
		$endpointRegistrar = $this->container->get(Endpoint\EndpointRegistrar::class);
		foreach ($allEndpoints as $endpoint) {
			$endpointRegistrar->register(class: $endpoint, factory: fn() => $this->container->get($endpoint));
		}

		// Collect and register Connectors.
		$allConnectors = $this->events->dispatch(new Events\CollectingConnectors([]))->connectors;
		$connectorRegistrar = $this->container->get(Connector\ConnectorRegistrar::class);
		foreach ($allConnectors as $connector) {
			$connectorRegistrar->register(class: $connector, factory: fn() => $this->container->get($connector));
		}

		// Collect and register Importers.
		$allImporters = $this->events->dispatch(new Events\CollectingImporters([]))->importers;
		$importerRegistrar = $this->container->get(Importer\ImporterRegistrar::class);
		foreach ($allImporters as $importer) {
			$importerRegistrar->register(class: $importer, factory: fn() => $this->container->get($importer));
		}

		// We're done with our part; fire the event!
		$this->events->dispatch(new Events\Startup($this));
	}

	/**
	 * Load classes from this library into the DI container.
	 *
	 * @return void
	 */
	private function loadContainerWithCoreClasses(): void {
		$this->container->addShared(Environment::class, fn() => $this->env);
		$this->container->addShared(Events\EventDispatcher::class, fn() => $this->events);
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

		$this->container->addShared(Container\Container::class);
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
			Connector\BeginAuthRequest::class => Connector\AuthRequestInitializer::class,
			Connector\FinishAuthRequest::class => Connector\AuthRequestFinalizer::class,
			Connector\RefreshChannels::class => Connector\ChannelRefresher::class,
			Importer\PullFromChannel::class => Importer\ImporterStarter::class,
		];
		return $map;
	}
}
