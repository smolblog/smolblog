<?php

namespace Smolblog\Core;

use Composer\InstalledVersions;

/**
 * The core app class.
 */
class App {
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
	 * @var Command\CommandBus
	 */
	public readonly Command\CommandBus $commands;

	/**
	 * Environment information
	 *
	 * @var Environment
	 */
	public readonly Environment $env;

	/**
	 * Array of PluginPackages that are installed (not necessarily active)
	 *
	 * @var Plugin\PluginPackage[]
	 */
	private array $installedPackages = [];

	/**
	 * Array of Plugins that are currently active
	 *
	 * @var Plugin\Plugin[]
	 */
	private array $activePlugins = [];

	/**
	 * Construct a new App. Requires an EndpointRegistrar and a loaded Environment object. Loads the
	 * dependency injection container (`$app->container`) with core classes and dependencies.
	 *
	 * Once this object is constructed, it is the responsibility of the bootstrapper to add any further
	 * dependencies to the container before calling `startup()`.
	 *
	 * @param Environment $withEnvironment Environment information.
	 */
	public function __construct(
		Environment $withEnvironment
	) {
		$this->env = $withEnvironment;

		$this->container = new Container\Container();
		$this->events = new Events\EventDispatcher();

		$this->loadContainerWithCoreClasses();

		$this->commands = new Command\CommandBus(
			map: $this->createCommandMap(),
			container: $this->container,
		);
	}

	/**
	 * Start the app!
	 *
	 * @return void
	 */
	public function startup(): void {
		// Load any plugins in the system.
		$this->loadPlugins();

		// Register endpoints with external system.
		$coreEndpoints = [
			Connector\ConnectCallback::class,
			Connector\ConnectInit::class,
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

		$this->container->addShared(Connector\ConnectorRegistrar::class);

		$this->container->addShared(Connector\ConnectionCredentialFactory::class);
		$this->container->addShared(Transient\TransientFactory::class);

		$this->container->add(Endpoints\ConnectCallback::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Transient\TransientFactory::class);
		$this->container->add(Endpoints\ConnectInit::class)->
			addArgument(Environment::class)->
			addArgument(Connector\ConnectorRegistrar::class)->
			addArgument(Transient\TransientFactory::class);
		$this->container->add(
			Plugin\InstalledPlugins::class,
			fn() => new Plugin\InstalledPlugins(
				installedPackages: $this->installedPackages,
				activePlugins: $this->activePlugins
			)
		);
	}

	/**
	 * Create a map of command classes to handlers.
	 *
	 * @return array
	 */
	private function createCommandMap(): array {
		$map = [];
		return $map;
	}

	/**
	 * Find the plugins from composer and load them
	 *
	 * @return void
	 */
	private function loadPlugins(): void {
		$plugins = InstalledVersions::getInstalledPackagesByType('smolblog-plugin');
		foreach (array_unique($plugins) as $packageName) {
			$package = Plugin\PluginPackage::createFromComposer($packageName);
			$this->installedPackages[] = $package;

			// In the future, we should check against a list of "activated" plugins.
			if (empty($package->errors)) {
				$plugin = $package->createPlugin(app: $this);
				if ($plugin) {
					$this->activePlugins[$packageName] = $plugin;
				}
			}
		}
	}
}
