<?php

namespace Smolblog\Foundation\Value\Jobs;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Test\TestCase;

#[CoversClass(AsyncExecutionJob::class)]
final class AsyncExecutionJobTest extends TestCase {
	public function testItCreatesTheJobWithTheCorrectParameters() {
		$command = $this->createStub(Command::class);
		$actual = new AsyncExecutionJob($command);
		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals(CommandBus::class, $actual->service);
		$this->assertEquals('execute', $actual->method);
		$this->assertEquals(['command' => $command], $actual->getParameters());
	}
}
