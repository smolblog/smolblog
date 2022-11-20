<?php

namespace Smolblog\App\Container;

interface ContainerDefinition {
	/**
	 * Add a constructor argument for the given definition.
	 *
	 * @param string $arg Class or factory to add to the definition.
	 * @return ContainerDefinition
	 */
	public function addArgument(string $arg): ContainerDefinition;
}
