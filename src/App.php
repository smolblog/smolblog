<?php

namespace Smolblog\Core;

use Smolblog\Core\Dependencies\{Container, ContainerDefinition, EndpointRegistrar, Environment, EventDispatcher};
use Smolblog\Core\Registrars\ConnectorRegistrar;

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
	 * @var EventDispatcher
	 */
	public readonly EventDispatcher $dispatcher;

	/**
	 * Endpoint registrar
	 *
	 * @var EndpointRegistrar
	 */
	private EndpointRegistrar $endpoints;

	/**
	 * Environment information
	 *
	 * @var Environment
	 */
	public readonly Environment $environment;

	/**
	 * Registrar for Connector objects (for social media connections)
	 *
	 * @var ConnectorRegistrar
	 */
	public readonly ConnectorRegistrar $connectors;

	/**
	 * Construct a new App. Requires a dependency injection container and event dispatcher.
	 *
	 * @param Container         $withContainer         Dependency Injection container.
	 * @param EventDispatcher   $withDispatcher        Event dispatcher.
	 * @param EndpointRegistrar $withEndpointRegistrar Endpoint registrar.
	 * @param Environment       $withEnvironment       Environment information.
	 */
	public function __construct(
		Container $withContainer,
		EventDispatcher $withDispatcher,
		EndpointRegistrar $withEndpointRegistrar,
		Environment $withEnvironment
	) {
		$this->container = $withContainer;
		$this->dispatcher = $withDispatcher;
		$this->endpoints = $withEndpointRegistrar;
		$this->environment = $withEnvironment;

		$this->connectors = new ConnectorRegistrar();
	}

	/**
	 * Classes to register with the container
	 *
	 * @var array
	 */
	protected $classes = [
		'Models' => [
			Models\ConnectionCredential::class,
			Models\User::class,
		],
		'Endpoints' => [
			Endpoints\ConnectCallback::class,
			Endpoints\ConnectInit::class,
		]
	];

	/**
	 * Start the app!
	 *
	 * @return void
	 */
	public function startup(): void {
		// Load classes into container.
		array_walk_recursive($this->classes, function ($class) {
			$this->container->add($class);
		});
		$this->dispatcher->dispatch(new Events\CoreClassesLoaded($this->container));

		// Register endpoints with external system.
		foreach ($this->classes['Endpoints'] as $endpoint) {
			$this->endpoints->registerEndpoint(new $endpoint($this));
		}

		// We're done with our part; fire the event!
		$this->dispatcher->dispatch(new Events\Startup($this));
	}
}
