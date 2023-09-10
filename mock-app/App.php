<?php

namespace Smolblog\Mock;

use Smolblog\Core\Model as CoreModel;
use Smolblog\Framework\Infrastructure\AppKit;
use Smolblog\Framework\Infrastructure\ServiceRegistry;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query;
use Smolblog\Mock\Model as MockModel;

final class App {
	use AppKit;

	private static ?App $internal = null;

	private static function getApp(): App {
		self::$internal ??= new App();
		return self::$internal;
	}

	public static function dispatch(object $message): void {
		self::getApp()->bus->dispatch($message);
	}

	public static function fetch(Query $message): mixed {
		return self::getApp()->bus->fetch($message);
	}

	public static function getService(string $service): mixed {
		return self::getApp()->container->get($service);
	}

	public readonly MessageBus $bus;
	public readonly ServiceRegistry $container;

	private function __construct() {
		$this->container = $this->buildDefaultContainer([
			CoreModel::class,
			MockModel::class,
		]);

		$this->bus = $this->container->get(MessageBus::class);
	}
}
