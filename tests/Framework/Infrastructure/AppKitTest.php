<?php

namespace Smolblog\Framework\Infrastructure;

use Psr\Container\ContainerInterface;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Service\Registry\Registry;

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
		ListenerRegistry::class => ['container' => ServiceRegistry::class],
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
			ListenerRegistry::class => [
				'container' => ServiceRegistry::class,
				'configuration' => fn() => [QueryMemoizationService::class, SecurityCheckService::class]
			],
			QueryMemoizationService::class => [],
			SecurityCheckService::class => ['messageBus' => MessageBus::class],
		];

		$app = new TestApp();
		$actual = $app->buildDependencyMap([BasicModel::class, ListenerModel::class]);
		$this->assertEquals($expected, $actual);
		$this->assertEquals(
			$expected[ListenerRegistry::class]['configuration'](),
			$actual[ListenerRegistry::class]['configuration'](),
		);
	}

	public function testItWillCreateFactoriesToCallConfigureOnNewRegistries() {
		$regModel = new class() extends DomainModel {
			public static function getDependencyMap(): array {
				$registryOne = new class() implements Registry {
					public function __construct(public ?ContainerInterface $container = null) {}
					public static function getInterfaceToRegister(): string { return Service::class; }
					public function configure(array $configuration): void {
						throw new \Exception('Test failure; configure() should not have been called');
					}
				};
				$registryTwo = new class() implements Registry {
					public array $config = [];
					public function __construct(public ?ContainerInterface $container = null) {}
					public static function getInterfaceToRegister(): string { return Service::class; }
					public function configure(array $configuration): void {
						$this->config = $configuration;
					}
				};

				class_alias(get_class($registryOne), 'AppKitTestRegistryOne');
				class_alias(get_class($registryTwo), 'AppKitTestRegistryTwo');

				return [
					'AppKitTestRegistryOne' => fn() => $registryOne,
					'AppKitTestRegistryTwo' => ['container' => ContainerInterface::class],
				];
			}
		};

		$app = new TestApp();
		$actual = $app->buildDependencyMap([BasicModel::class, get_class($regModel)]);
		$stubContainer = $this->createStub(ServiceRegistry::class);
		$stubContainer->method('get')->willReturnSelf();
		$stubContainer->method('instantiateService')->willReturnCallback(
			fn($srv, $conf) => new $srv($stubContainer)
		);

		$this->assertIsCallable($actual['AppKitTestRegistryOne']);
		$this->assertIsCallable($actual['AppKitTestRegistryTwo']);

		$reg1 = call_user_func($actual['AppKitTestRegistryOne'], $stubContainer);
		$reg2 = call_user_func($actual['AppKitTestRegistryTwo'], $stubContainer);

		$this->assertNull($reg1->container);
		$this->assertInstanceOf(ContainerInterface::class, $reg2->container);
		$this->assertEquals(['AppKitTestRegistryOne', 'AppKitTestRegistryTwo'], $reg2->config);
	}

	public function testItWillCreateAServiceRegistryWithTheDefaultModel() {
		$app = new TestApp();
		$container = $app->buildDefaultContainer();

		foreach (array_keys(DefaultModel::SERVICES) as $srv) {
			$this->assertTrue($container->has($srv));
		}
	}
}
