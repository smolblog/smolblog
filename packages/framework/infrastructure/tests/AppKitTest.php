<?php

namespace Smolblog\Infrastructure;

use Crell\Tukio\Dispatcher;
use Crell\Tukio\OrderedListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Service\KeypairGenerator;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Infrastructure\Registries\CommandHandlerRegistry;
use Smolblog\Infrastructure\Registries\RegistryHelper;
use Smolblog\Test\TestCase;

final class AppKitTestSampleImplementation {
	use AppKit {
		buildContainerFromModels as public;
		buildDependencyMap as public;
	}
}

readonly class AppKitTestExampleCommand extends Command {
	public function __construct(public string $name) {}
}

final class AppKitTestExampleCommandHandler implements CommandHandlerService {
	#[CommandHandler]
	public function handleExampleCommand(AppKitTestExampleCommand $cmd) {
		return "The command {$cmd->name} has been handled.";
	}
}

final class AppKitTest extends TestCase {
	private AppKitTestSampleImplementation $testApp;

	protected function setUp(): void {
		$this->testApp = new AppKitTestSampleImplementation();
	}

	public function testDependencyMap() {
		$testModel = new class() extends DomainModel {
			const AUTO_SERVICES = [KeypairGenerator::class, OrderedListenerProvider::class];
			const SERVICES = [ListenerProviderInterface::class => OrderedListenerProvider::class];
		};

		$models = [
			Model::class,
			get_class($testModel),
		];

		$expected = [
			Registries\CommandHandlerRegistry::class => ['container' => ContainerInterface::class],
			Registries\EventListenerRegistry::class => ['container' => ContainerInterface::class],
			LoggerInterface::class => NullLogger::class,
			EventDispatcherInterface::class => Dispatcher::class,
			CommandBus::class => Registries\CommandHandlerRegistry::class,
			NullLogger::class => [],
			Dispatcher::class => [
				ListenerProviderInterface::class,
				LoggerInterface::class,
			],
			KeypairGenerator::class => [],
			OrderedListenerProvider::class => ['container' => ContainerInterface::class],
			ListenerProviderInterface::class => OrderedListenerProvider::class,
		];

		$this->assertEquals($expected, $this->testApp->buildDependencyMap($models));
	}

	public function testContainerSetup() {
		$testModel = new class() extends DomainModel {
			const AUTO_SERVICES = [AppKitTestExampleCommandHandler::class];
		};

		$testMap = $this->testApp->buildDependencyMap([
			Model::class,
			get_class($testModel),
		]);
		$testConfigs = RegistryHelper::getRegistryConfigs(array_keys($testMap));

		$this->assertEquals([AppKitTestExampleCommandHandler::class], $testConfigs[CommandHandlerRegistry::class]);

		$container = $this->testApp->buildContainerFromModels([
			Model::class,
			get_class($testModel),
		]);
		$id = $this->randomId()->toString();
		$command = new AppKitTestExampleCommand(name: $id);
		$expected = "The command $id has been handled.";

		$this->assertEquals($expected, $container->get(CommandBus::class)->execute($command));
	}
}
