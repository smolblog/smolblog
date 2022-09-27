<?php

namespace Smolblog\Core;

use Psr\Container\ContainerInterface;
use League\Container\Container as LeagueContainer;
use League\Container\Definition\Definition as LeagueDefinition;

/**
 * Basic dependency injection container for Smolblog.
 */
class Container implements ContainerInterface {
	/**
	 * Internal instance of a League\Container\Container
	 *
	 * @var LeagueContainer
	 */
	private LeagueContainer $internal;

	/**
	 * Create a new Container
	 */
	public function __construct() {
		$this->internal = new LeagueContainer();
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
	 * @throws ContainerExceptionInterface Error while retrieving the entry.
	 *
	 * @return mixed Entry.
	 */
	public function get(string $id): mixed {
		return $this->internal->get($id);
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return boolean
	 */
	public function has(string $id): bool {
		return $this->internal->has($id);
	}

	/**
	 * Add a class to be tracked by the Container
	 *
	 * @param string   $id      Fully qualified class name.
	 * @param callable $factory Optional factory callable.
	 * @return ContainerDefinition
	 */
	public function add(string $id, callable $factory = null): ContainerDefinition {
		if (isset($factory)) {
			return $this->wrap($this->internal->add($id, $factory));
		}
		return $this->wrap($this->internal->add($id));
	}

	/**
	 * Add a class to be tracked by the Container. Will only ever give one instance of the class.
	 *
	 * @param string   $id      Fully qualified class name.
	 * @param callable $factory Optional factory callable.
	 * @return ContainerDefinition
	 */
	public function addShared(string $id, callable $factory = null): ContainerDefinition {
		if (isset($factory)) {
			return $this->wrap($this->internal->addShared($id, $factory));
		}
		return $this->wrap($this->internal->addShared($id));
	}

	/**
	 * Add to a definition already in the Container
	 *
	 * @param string $id Fully qualiifed class name.
	 * @return ContainerDefinition
	 */
	public function extend(string $id): ContainerDefinition {
		return $this->wrap($this->internal->extend($id));
	}

	/**
	 * Wrap a League Definition in a Smolblog ContainerDefinition. This exists pretty much
	 * to restrict the API interface future versions will need to support.
	 *
	 * @param LeagueDefinition $def Definition from a League Container.
	 * @return ContainerDefinition
	 */
	private function wrap(LeagueDefinition $def): ContainerDefinition {
		return new class ($def) implements ContainerDefinition {
			/**
			 * Construct from a League Definition
			 *
			 * @param LeagueDefinition $internal League Definition.
			 */
			public function __construct(private LeagueDefinition $internal) {
			}

			/**
			 * Pass to the internal League Definition's addArgument, then wrap it in
			 * this class.
			 *
			 * @param string $arg Argument to add to the definition.
			 * @return ContainerDefinition
			 */
			public function addArgument(string $arg): ContainerDefinition {
				$thisClass = get_class();
				return new $thisClass($this->internal->addArgument($arg));
			}
		};
	}
}
