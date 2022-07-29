<?php

namespace Smolblog\Core;

use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface {
	/**
	 * Add a class to be tracked by the Container
	 *
	 * @param string $id Fully qualified class name.
	 * @return ContainerDefinition
	 */
	public function add(string $id): ContainerDefinition;

	/**
	 * Add a class to be tracked by the Container. Will only ever give one instance of the class.
	 *
	 * @param string $id Fully qualified class name.
	 * @return ContainerDefinition
	 */
	public function addShared(string $id): ContainerDefinition;

	/**
	 * Add to a definition already in the Container
	 *
	 * @param string $id Fully qualiifed class name.
	 * @return ContainerDefinition
	 */
	public function extend(string $id): ContainerDefinition;
}
