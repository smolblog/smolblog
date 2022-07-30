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

		$this->container->addShared(Environment::class, function () use ($withEnvironment) {
			return $withEnvironment;
		});
		$this->container->addShared(EndpointRegistrar::class, function () use ($withEndpointRegistrar) {
			return $withEndpointRegistrar;
		});
		$this->container->addShared(EventDispatcher::class);

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
	}

	/**
	 * Start the app!
	 *
	 * @return void
	 */
	public function startup(): void {
		// Register endpoints with external system.
		foreach ($this->classes['Endpoints'] as $endpoint) {
			$this->endpoints->registerEndpoint($container->get($endpoint));
		}

		// We're done with our part; fire the event!
		$this->dispatcher->dispatch(new Events\Startup($this));
	}
}
