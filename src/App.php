<?php

namespace Smolblog\Core;

use Smolblog\Core\Registrars\ConnectorRegistrar;
use League\Container\Container as LeagueContainer;

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
	 * @param EventDispatcher   $withDispatcher        Event dispatcher.
	 * @param EndpointRegistrar $withEndpointRegistrar Endpoint registrar.
	 * @param Environment       $withEnvironment       Environment information.
	 */
	public function __construct(
		EventDispatcher $withDispatcher,
		EndpointRegistrar $withEndpointRegistrar,
		Environment $withEnvironment
	) {
		$this->dispatcher = $withDispatcher;
		$this->endpoints = $withEndpointRegistrar;
		$this->environment = $withEnvironment;

		$this->connectors = new ConnectorRegistrar();

		$leagueContainer = new LeagueContainer();
		$leagueContainer->addShared(Environment::class, function () use ($withEnvironment) {
			return $withEnvironment;
		});
		$this->container = $leagueContainer;
	}

	/**
	 * Classes to register with the container
	 *
	 * @var array
	 */
	protected $classes = [
		'Endpoints' => [
			Endpoints\ConnectCallback::class,
			Endpoints\ConnectInit::class,
		],
		'Factories' => [
			Factories\ConnectionCredentialFactory::class,
			Factories\TransientFactory::class,
		],
		'Registrars' => [
			Registrars\ConnectorRegistrar::class,
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

		// Set up what we know.
		$this->container->extend(Endpoints\ConnectCallback::class)->
			addArgument(Registrars\ConnectorRegistrar::class)->
			addArgument(Factories\TransientFactory::class);
		$this->container->extend(Endpoints\ConnectInit::class)->
			addArgument(Environment::class)->
			addArgument(Registrars\ConnectorRegistrar::class)->
			addArgument(Factories\TransientFactory::class);

		// Done with container setup.
		$this->dispatcher->dispatch(new Events\CoreClassesLoaded($this->container));

		// Register endpoints with external system.
		foreach ($this->classes['Endpoints'] as $endpoint) {
			$this->endpoints->registerEndpoint($container->get($endpoint));
		}

		// We're done with our part; fire the event!
		$this->dispatcher->dispatch(new Events\Startup($this));
	}
}
