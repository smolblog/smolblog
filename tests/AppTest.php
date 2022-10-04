<?php

namespace Smolblog\Core;

use Smolblog\Core\Events\Startup;
use PHPUnit\Framework\TestCase;

class TestPlugin implements Plugin\Plugin {
	public static function config(): Plugin\PluginPackage {
		return new Plugin\PluginPackage(
			package: 'smolblog/test',
			version: '1.0.0',
			title: 'Smolblog Test Plugin',
			description: 'A test plugin for a test system.',
		);
	}

	public static function setup(App $app) {}
}

abstract class TestConnector implements Connector\Connector {
	public static function config(): Connector\ConnectorConfig {
		return new Connector\ConnectorConfig(slug: 'test');
	}
}

final class AppTest extends TestCase {
	public function testItCanBeInstantiated(): void {
		$environment = new Environment(apiBase: 'https://smol.blog/api/');

		$app = new App(
			withEnvironment: $environment,
			pluginClasses: [],
		);
		$this->assertInstanceOf(App::class, $app);
	}

	public function testItCanBeStartedWithMinimalConfig(): void {
		$environment = new Environment(apiBase: 'https://smol.blog/api/');

		$app = new App(
			withEnvironment: $environment,
			pluginClasses: [],
		);

		$app->container->addShared(Endpoint\EndpointRegistrar::class, fn() => $this->createStub(Endpoint\EndpointRegistrar::class));

		$callbackHit = false;
		$app->events->subscribeTo(
			Startup::class,
			function($event) use (&$callbackHit) {
				$this->assertInstanceOf(Startup::class, $event);
				$callbackHit = true;
			}
		);

		$app->startup();
		$this->assertTrue($callbackHit);
	}

	public function testItCanBeStartedWithAllClassesConfigured(): void {
		$environment = new Environment(apiBase: 'https://smol.blog/api/');

		$app = new App(
			withEnvironment: $environment,
			pluginClasses: [TestPlugin::class],
		);

		$app->container->addShared(Endpoint\EndpointRegistrar::class, fn() => $this->createStub(Endpoint\EndpointRegistrar::class));

		$mockConnector = $this->createStub(TestConnector::class);
		$app->container->addShared(TestConnector::class, fn() => $mockConnector);
		$app->events->subscribeTo(Events\CollectingConnectors::class, fn($event) => $event->connectors[] = TestConnector::class);

		$callbackHit = false;
		$app->events->subscribeTo(
			Startup::class,
			function($event) use (&$callbackHit) {
				$this->assertInstanceOf(Startup::class, $event);
				$callbackHit = true;
			}
		);

		$app->startup();
		$this->assertTrue($callbackHit);

		// InstalledPlugins endpoint is added with an arrow function;
		// calling that endpoint here so the arrow function is covered.
		$this->assertInstanceOf(Plugin\InstalledPlugins::class, $app->container->get(Plugin\InstalledPlugins::class));
	}
}
