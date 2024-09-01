<?php

namespace Smolblog\Test;

use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Framework\Infrastructure\AppKit;
use Smolblog\Framework\Infrastructure\DefaultModel;
use Smolblog\Framework\Infrastructure\ServiceRegistry;

class TestApp {
	use AppKit;

	public readonly ServiceRegistry $container;

	public function __construct(array $models, array $services) {
		$this->container = new ServiceRegistry(
			$this->buildDependencyMapFromArrays([
				DefaultModel::getDependencyMap(),
				...array_map(fn($model) => $model::getDependencyMap(), $models),
				$services,
			])
		);
	}

	public function execute(Command $command): mixed {
		return $this->container->get(CommandBus::class)->execute($command);
	}
}
