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
	 * @var Container
	 */
	public readonly Container $container;

	/**
	 * Event dispatcher
	 *
	 * @var Container
	 */
	public readonly EventDispatcher $events;

	/**
	 * Array of PluginPackages that are installed (not necessarily active)
	 *
	 * @var PluginPackage[]
	 */
	private array $installedPackages = [];

	/**
	 * Array of Plugins that are currently active
	 *
	 * @var Plugin[]
	 */
	private array $activePlugins = [];

	/**
	 * Construct a new App. Requires an EndpointRegistrar and a loaded Environment object. Loads the
	 * dependency injection container (`$app->container`) with core classes and dependencies.
	 *
	 * Once this object is constructed, it is the responsibility of the bootstrapper to add any further
	 * dependencies to the container before calling `startup()`.
	 *
	 * @param EndpointRegistrar $withEndpointRegistrar Endpoint registrar.
	 * @param Environment       $withEnvironment       Environment information.
	 */
	public function __construct(
		EndpointRegistrar $withEndpointRegistrar,
		Environment $withEnvironment
	) {
		$this->endpoints = $withEndpointRegistrar;
		$this->environment = $withEnvironment;

		$this->container = new Container();
		$this->events = new EventDispatcher();

		$this->container->addShared(Environment::class, fn() => $withEnvironment);
		$this->container->addShared(EndpointRegistrar::class, fn() => $withEndpointRegistrar);
		$this->container->addShared(EventDispatcher::class, fn() => $this->events);

		$this->container->addShared(Registrars\ConnectorRegistrar::class);

		$this->container->addShared(Factories\ConnectionCredentialFactory::class);
		$this->container->addShared(Factories\TransientFactory::class);

		$this->container->add(Endpoints\ConnectCallback::class)->
			addArgument(Registrars\ConnectorRegistrar::class)->
			addArgument(Factories\TransientFactory::class);
		$this->container->add(Endpoints\ConnectInit::class)->
			addArgument(Environment::class)->
			addArgument(Registrars\ConnectorRegistrar::class)->
			addArgument(Factories\TransientFactory::class);
		$this->container->add(
			Endpoints\InstalledPlugins::class,
			fn() => new Endpoints\InstalledPlugins(
				installedPackages: $this->installedPackages,
				activePlugins: $this->activePlugins
			)
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
			Endpoints\ConnectCallback::class,
			Endpoints\ConnectInit::class,
			Endpoints\InstalledPlugins::class,
		];
		$allEndpoints = $this->events->dispatch(new Events\CollectingEndpoints($coreEndpoints))->endpoints;
		$endpointRegistrar = $this->container->get(EndpointRegistrar::class);
		foreach ($allEndpoints as $endpoint) {
			$endpointRegistrar->registerEndpoint($this->container->get($endpoint));
		}

		// Collect and register Connectors.
		$allConnectors = $this->events->dispatch(new Events\CollectingConnectors([]))->connectors;
		$connectorRegistrar = $this->container->get(Registrars\ConnectorRegistrar::class);
		foreach ($allConnectors as $connector) {
			$connectorRegistrar->register($this->container->get($connector));
		}

		// We're done with our part; fire the event!
		$dispatcher = $this->container->get(EventDispatcher::class);
		$dispatcher->dispatch(new Events\Startup($this));
	}

	/**
	 * Find the plugins from composer and load them
	 *
	 * @return void
	 */
	private function loadPlugins(): void {
		$plugins = InstalledVersions::getInstalledPackagesByType('smolblog-plugin');
		foreach (array_unique($plugins) as $packageName) {
			$package = PluginPackage::createFromComposer($packageName);
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
