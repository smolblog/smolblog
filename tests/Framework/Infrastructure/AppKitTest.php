<?php

namespace Smolblog\Framework\Infrastructure;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

final class TestApp {
	use AppKit {
		buildDefaultContainer as public;
		buildDependencyMap as public;
	}
}

final class BasicModel extends DomainModel {
	const SERVICES = [
		TestApp::class => [],
		AppKitTest::class => ['stub' => TestApp::class],
	];
}

final class ListenerModel extends DomainModel {
	const SERVICES = [
		ListenerRegistrar::class => ['container' => ServiceRegistry::class],
		QueryMemoizationService::class => [],
		SecurityCheckService::class => ['messageBus' => MessageBus::class],
	];
}

final class AppKitTest extends TestCase {
	public function testItCorrectlyJoinsModels() {
		$modelOneOverride = new class extends DomainModel {
			const SERVICES = [
				AppKitTest::class => ['stub' => ListenerModel::class],
			];
		};

		$expected = [
			TestApp::class => [],
			AppKitTest::class => ['stub' => ListenerModel::class],
		];

		$app = new TestApp();
		$this->assertEquals($expected, $app->buildDependencyMap([BasicModel::class, get_class($modelOneOverride)]));
	}

	public function testItWillAddConfigurationToRegistries() {
		$expected = [
			TestApp::class => [],
			AppKitTest::class => ['stub' => TestApp::class],
			ListenerRegistrar::class => [
				'container' => ServiceRegistry::class,
				'configuration' => [QueryMemoizationService::class, SecurityCheckService::class]
			],
			QueryMemoizationService::class => [],
			SecurityCheckService::class => ['messageBus' => MessageBus::class],
		];

		$app = new TestApp();
		$this->assertEquals($expected, $app->buildDependencyMap([BasicModel::class, ListenerModel::class]));
	}

	public function testItWillCreateAServiceRegistryWithTheDefaultModel() {
		$app = new TestApp();
		$container = $app->buildDefaultContainer();

		foreach (array_keys(DefaultModel::SERVICES) as $srv) {
			$this->assertTrue($container->has($srv));
		}
	}
}
