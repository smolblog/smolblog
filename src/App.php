<?php

namespace Smolblog\Core;

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
	public readonly EndpointRegistrar $endpoints;

	/**
	 * Construct a new App. Requires a dependency injection container and event dispatcher.
	 *
	 * @param Container         $withContainer         Dependency Injection container.
	 * @param EventDispatcher   $withDispatcher        Event dispatcher.
	 * @param EndpointRegistrar $withEndpointRegistrar Endpoint registrar.
	 */
	public function __construct(
		Container $withContainer,
		EventDispatcher $withDispatcher,
		EndpointRegistrar $withEndpointRegistrar
	) {
		$this->container = $withContainer;
		$this->dispatcher = $withDispatcher;
		$this->endpoints = $withEndpointRegistrar;
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

		// Register endpoints with external system.
		foreach ($this->classes['Endpoints'] as $endpoint) {
			$this->endpoints->registerEndpoint(new $endpoint($this));
		}

		// We're done with our part; fire the event!
		$this->dispatcher->dispatch(new Events\Startup($this));
	}
}
