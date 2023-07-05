<?php

namespace Smolblog\Mock;

use PDO;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Services\ConnectorRegistry;
use Smolblog\Core\Model;
use Smolblog\Framework\Infrastructure\DefaultMessageBus;
use Smolblog\Framework\Infrastructure\ListenerRegistry;
use Smolblog\Framework\Infrastructure\SecurityCheckService;
use Smolblog\Framework\Infrastructure\ServiceRegistry;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query;
use Smolblog\Markdown\SmolblogMarkdown;
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

	public static function getService(string $service): mixed {
		return self::getApp()->container->get($service);
	}

	public readonly MessageBus $bus;
	public readonly ServiceRegistry $container;

	private function __construct() {
		$pdo = $this->makeDatabase();

		$models = [
			Model::class,
			MockModel::class,
		];

		$appServices = [
			MessageBus::class => DefaultMessageBus::class,
			DefaultMessageBus::class => fn() => $this->bus,
			ContainerInterface::class => ServiceRegistry::class,
			Connector::class => [],
			PDO::class => fn() => $pdo,
			MockMemoService::class => [],
			SecurityCheckService::class => [MessageBus::class],
			SmolblogMarkdown::class => [],
		];

		$services = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::SERVICES), []);
		$services = array_merge($services, $appServices);
		$services[ConnectorRegistry::class]['configuration'] = fn() => ['smolblog' => Connector::class];

		$this->container = new ServiceRegistry(configuration: $services);
		$registry = new ListenerRegistry(container: $this->container);

		$listeners = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::LISTENERS), []);
		array_walk($listeners, fn($className) => $registry->registerService($className));
		$registry->registerService(MockMemoService::class);
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

		DROP TABLE IF EXISTS "content_events";
		CREATE TABLE "content_events" ("id" integer,"event_id" text NOT NULL,"event_time" text NOT NULL,"content_id" text NOT NULL,"site_id" text NOT NULL,"user_id" text NOT NULL,"payload" text,"event_type" text NOT NULL, PRIMARY KEY (id));

		DROP TABLE IF EXISTS "temp_options";
		CREATE TABLE "temp_options" ("id" integer,"key" text NOT NULL,"value" text NOT NULL,"expires" text NOT NULL, PRIMARY KEY (id));

		DROP TABLE IF EXISTS "standard_content";
		CREATE TABLE "standard_content" ("id" integer, "content_id" TEXT NOT NULL, "content_type" TEXT NOT NULL, "title" TEXT NOT NULL, "body" TEXT NOT NULL, "permalink" TEXT, "publish_timestamp" TEXT, "visibility" TEXT NOT NULL, "author_id" TEXT NOT NULL, "site_id" TEXT NOT NULL, "extensions" TEXT NOT NULL, PRIMARY KEY (id));

		DROP TABLE IF EXISTS "notes";
		CREATE TABLE "notes" ("id" integer, "content_id" TEXT NOT NULL, "body" TEXT NOT NULL, PRIMARY KEY (id));

		EOF;

		$db->exec($setupSql);

		return $db;
	}
}
