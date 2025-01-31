<?php

namespace Smolblog\Infrastructure\Registries;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Infrastructure\Model;
use Smolblog\Test\AppTest;
use Smolblog\Test\TestCase;

/**
 * Use this to create a mock that we can use to see what has been called.
 */
interface MockCommandHandler {
	public function commandOneHandled($cmd);
	public function commandTwoHandled($cmd);
	public function commandThreeHandled($cmd);
	public function commandFourOrFiveHandled($cmd);
}

final readonly class CommandOne extends Command {
	public function __construct(public string $word, public int $num) {}
}

final readonly class CommandTwo extends Command {
	public function __construct(public string $word, public int $num) {}
}

final readonly class CommandThree extends Command {
	public function __construct(public string $word, public int $num) {}
}

final readonly class CommandFour extends Command {
	public function __construct(public string $word, public int $num) {}
}

final readonly class CommandFive extends Command {
	public function __construct(public string $word, public int $num) {}
}

final class CommandHandlerOne implements CommandHandlerService {
	public function __construct(private MockCommandHandler $mock) {}
	#[CommandHandler]
	public function doCommandOne(CommandOne $cmd) { return $this->mock->commandOneHandled($cmd); }
	#[CommandHandler]
	public function doCommandTwo(CommandTwo $cmd) { return $this->mock->commandTwoHandled($cmd); }
}

final class CommandHandlerTwo implements CommandHandlerService {
	public function __construct(private MockCommandHandler $mock) {}
	#[CommandHandler]
	public function doCommandThree(CommandThree $cmd) { return $this->mock->commandThreeHandled($cmd); }
	#[CommandHandler]
	public function doCommandsFourOrFive(CommandFour|CommandFive $cmd) { return $this->mock->commandFourOrFiveHandled($cmd); }
}

final class CommandHandlerRegistryTest extends AppTest {
	const INCLUDED_MODELS = [Model::class];

	private MockCommandHandler & MockObject $mockHandler;

	protected function createMockServices(): array
	{
		$this->mockHandler = $this->createMock(MockCommandHandler::class);

		return [
			CommandHandlerOne::class => [MockCommandHandler::class],
			CommandHandlerTwo::class => [MockCommandHandler::class],
			MockCommandHandler::class => fn() => $this->mockHandler,
		];
	}

	public function testItRunsACommandAndReturnsTheValue() {
		$service = $this->app->container->get(CommandBus::class);
		$cmd = new CommandTwo(word: 'up', num: 34);

		$this->mockHandler->expects($this->once())->method('commandTwoHandled')->with($cmd)->willReturn('two');
		$this->mockHandler->expects($this->never())->method('commandOneHandled');

		$result = $service->execute($cmd);
		$this->assertEquals('two', $result);
	}

	public function testItCorrectlyRegistersAUnionTypeInAHandler() {
		$service = $this->app->container->get(CommandBus::class);
		$cmd = new CommandFive(word: 'up', num: 34);

		$this->mockHandler->expects($this->once())->method('commandFourOrFiveHandled')->with($cmd)->willReturn('five');
		$this->mockHandler->expects($this->never())->method('commandThreeHandled');

		$result = $service->execute($cmd);
		$this->assertEquals('five', $result);
	}

	public function testItThrowsAnExceptionIfNoHandlerExists() {
		$this->expectException(Exception::class);

		$this->app->container->get(CommandBus::class)->execute(new readonly class() extends Command {});
	}

	public function testAHandlerMustHaveARequiredParameter() {
		$this->expectException(CodePathNotSupported::class);

		$problemHandler = new class() implements CommandHandlerService {
			#[CommandHandler] public function incorrect(?CommandOne $cmd = null) {}
		};
		$this->app->container->get(CommandHandlerRegistry::class)->configure([get_class($problemHandler)]);
	}

	public function testAHandlerMustNotHaveOtherRequiredParameters() {
		$this->expectException(CodePathNotSupported::class);

		$problemHandler = new class() implements CommandHandlerService {
			#[CommandHandler] public function incorrect(CommandOne $cmd, string $config) {}
		};
		$this->app->container->get(CommandHandlerRegistry::class)->configure([get_class($problemHandler)]);
	}

	public function testAHandlerMustBeTypeHintedToACommand() {
		$this->expectException(CodePathNotSupported::class);

		$problemHandler = new class() implements CommandHandlerService {
			#[CommandHandler] public function incorrect($cmd) {}
		};
		$this->app->container->get(CommandHandlerRegistry::class)->configure([get_class($problemHandler)]);
	}

	public function testAHandlerCannotRequireAnIntersectionType() {
		$this->expectException(CodePathNotSupported::class);

		$problemHandler = new class() implements CommandHandlerService {
			#[CommandHandler] public function incorrect(CommandOne & CommandTwo $cmd) {}
		};
		$this->app->container->get(CommandHandlerRegistry::class)->configure([get_class($problemHandler)]);
	}
}
