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
	public readonly EndpointRegistrar $endpointRegistrar;

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
		$this->endpointRegistrar = $withEndpointRegistrar;
	}

	/**
	 * Models that will need dependencies defined
	 *
	 * @var array
	 */
	protected $models = [
		Models\ConnectionCredential::class,
		Models\User::class,
	];

	/**
	 * REST endpoints that need to be registered to the framework
	 *
	 * @var array
	 */
	protected $endpoints = [
		Endpoints\ConnectCallback::class,
		Endpoints\ConnectInit::class,
	];

	/**
	 * Start the app!
	 *
	 * @return void
	 */
	public function startup(): void {
		// Load classes into container.
		foreach ([...$models, ...$endpoints] as $model) {
			$this->container->add($model);
		}

		// Register endpoints with external system.
		foreach ($endpoints as $endpoint) {
			$this->endpointRegistrar->registerEndpoint(new $endpoint($this));
		}

		// We're done with our part; fire the event!
		$this->dispatcher->dispatch(new Events\Startup($this));
	}
}
