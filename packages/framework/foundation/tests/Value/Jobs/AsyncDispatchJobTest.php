<?php

namespace Smolblog\Foundation\Value\Jobs;

use PHPUnit\Framework\Attributes\CoversClass;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\TestCase;

#[CoversClass(AsyncDispatchJob::class)]
final class AsyncDispatchJobTest extends TestCase {
	public function testItCreatesTheJobWithTheCorrectParameters() {
		$event = $this->createStub(DomainEvent::class);
		$actual = new AsyncDispatchJob($event);
		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals(EventDispatcherInterface::class, $actual->service);
		$this->assertEquals('dispatch', $actual->method);
		$this->assertEquals(['event' => $event], $actual->getParameters());
	}
}
