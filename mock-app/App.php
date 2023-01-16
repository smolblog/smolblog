<?php

namespace Smolblog\Mock;

use PDO;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Services\ConnectorRegistrar;
use Smolblog\Core\Model;
use Smolblog\Framework\Infrastructure\DefaultMessageBus;
use Smolblog\Framework\Infrastructure\ListenerRegistrar;
use Smolblog\Framework\Infrastructure\ServiceRegistrar;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query;
use Smolblog\Mock\Model as MockModel;

final class App {
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

	public readonly MessageBus $bus;

	private function __construct() {
		$models = [
			Model::class,
			MockModel::class,
		];

		$appServices = [
			MessageBus::class => DefaultMessageBus::class,
			DefaultMessageBus::class => fn() => $this->bus,
			ContainerInterface::class => ServiceRegistrar::class,
			Connector::class => [],
			PDO::class => fn() => new PDO(__DIR__ . '/app.sqlite'),
		];

		$services = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::SERVICES), []);
		$services = array_merge($services, $appServices);
		$services[ConnectorRegistrar::class]['configuration'] = fn() => ['smolblog' => Connector::class];

		echo "Services:\n";
		print_r($services);
		$container = new ServiceRegistrar(configuration: $services);
		$registry = new ListenerRegistrar(container: $container);

		$listeners = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::LISTENERS), []);
		array_walk($listeners, fn($className) => $registry->registerService($className));

		$this->bus = new DefaultMessageBus($registry);
	}
}
