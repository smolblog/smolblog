<?php

namespace Smolblog\Mock;

use PDO;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Services\ConnectorRegistrar;
use Smolblog\Core\Model;
use Smolblog\Framework\Infrastructure\DefaultMessageBus;
use Smolblog\Framework\Infrastructure\ListenerRegistrar;
use Smolblog\Framework\Infrastructure\QueryMemoizationService;
use Smolblog\Framework\Infrastructure\SecurityCheckService;
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
		$pdo = $this->makeDatabase();

		$models = [
			Model::class,
			MockModel::class,
		];

		$appServices = [
			MessageBus::class => DefaultMessageBus::class,
			DefaultMessageBus::class => fn() => $this->bus,
			ContainerInterface::class => ServiceRegistrar::class,
			Connector::class => [],
			PDO::class => fn() => $pdo,
			QueryMemoizationService::class => [],
			SecurityCheckService::class => [MessageBus::class],
		];

		$services = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::SERVICES), []);
		$services = array_merge($services, $appServices);
		$services[ConnectorRegistrar::class]['configuration'] = fn() => ['smolblog' => Connector::class];

		$container = new ServiceRegistrar(configuration: $services);
		$registry = new ListenerRegistrar(container: $container);

		$listeners = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::LISTENERS), []);
		array_walk($listeners, fn($className) => $registry->registerService($className));
		$registry->registerService(QueryMemoizationService::class);
		$registry->registerService(SecurityCheckService::class);

		$this->bus = new DefaultMessageBus($registry);
	}

	private function makeDatabase(): PDO {
		$db = new PDO('sqlite:' . __DIR__ . '/app.sqlite');

		$setupSql = <<<EOF
		DROP TABLE IF EXISTS "channels";
		CREATE TABLE "channels" ("id" integer,"channel_id" text,"connection_id" text,"channel_key" text,"display_name" text,"details" text, PRIMARY KEY (id));

		DROP TABLE IF EXISTS "connections";
		CREATE TABLE "connections" ("id" integer,"connection_id" text,"user_id" text,"provider" text,"provider_key" text,"display_name" text,"details" text, PRIMARY KEY (id));

		DROP TABLE IF EXISTS "connector_events";
		CREATE TABLE "connector_events" ("id" integer,"event_id" text NOT NULL,"event_time" text NOT NULL,"connection_id" text NOT NULL,"user_id" text NOT NULL,"payload" text,"event_type" text NOT NULL, PRIMARY KEY (id));

		DROP TABLE IF EXISTS "temp_options";
		CREATE TABLE "temp_options" ("id" integer,"key" text NOT NULL,"value" text NOT NULL,"expires" text NOT NULL, PRIMARY KEY (id));
		EOF;

		$db->exec($setupSql);

		return $db;
	}
}
