<?php

namespace Smolblog\Core\Command;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Container\Container;

final class RunCommandTest implements Command {
	public function __construct(
		public readonly bool $return,
		public readonly string $payload
	) {}
}

final class CommandTestHandler {
	public function __construct(private $callback) {}

	public function handleRunCommandTest(RunCommandTest $command) {
		$retval = call_user_func($this->callback, $command);
		if ($command->return) {
			return $retval;
		}
	}
}

final class CommandBusTest extends TestCase {
	protected CommandBus $bus;
	protected CommandTestHandler $handler;

	public function testItRunsTheCorrectMethodInTheHandler() {
		$callbackCalled = false;
		$givenPayload = uniqid();
		$handler = new CommandTestHandler(
			callback: function(RunCommandTest $command) use(&$callbackCalled, $givenPayload) {
				$callbackCalled = true;
				$this->assertEquals($givenPayload, $command->payload);
			}
		);

		$container = $this->createMock(Container::class);
		$container->expects($this->once())
		          ->method('get')
							->with($this->equalTo(CommandTestHandler::class))
							->willReturn($handler);

		$bus = new CommandBus(
			container: $container,
			map: [RunCommandTest::class => CommandTestHandler::class],
		);

		$bus->handle(new RunCommandTest(return: false, payload: $givenPayload));
		$this->assertTrue($callbackCalled);
	}

	public function testItReturnsAnyResponseFromTheHandler() {
		$testString = uniqid();
		$handler = new CommandTestHandler(callback: fn() => $testString);

		$container = $this->createMock(Container::class);
		$container->expects($this->once())
		          ->method('get')
							->with($this->equalTo(CommandTestHandler::class))
							->willReturn($handler);

		$bus = new CommandBus(
			container: $container,
			map: [RunCommandTest::class => CommandTestHandler::class],
		);

		$response = $bus->handle(new RunCommandTest(return: true, payload: 'ignore this'));
		$this->assertEquals($testString, $response);
	}
}
