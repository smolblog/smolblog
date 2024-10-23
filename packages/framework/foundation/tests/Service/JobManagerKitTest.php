<?php

namespace Smolblog\Foundation\Service\Job;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Foundation\Value\Jobs\AsyncDispatchJob;
use Smolblog\Foundation\Value\Jobs\AsyncExecutionJob;
use Smolblog\Foundation\Value\Jobs\Job;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\TestCase;

final class TestJobManager implements JobManager {
	use JobManagerKit;
	public function __construct(private mixed $expected) {}
	public function enqueue(Job $job): void { TestCase::assertEquals($this->expected, $job); }
}

#[CoversClass(JobManagerKit::class)]
final class JobManagerKitTest extends TestCase {
	public function testItCreatesAnAsyncExecutionJobForACommand() {
		$command = $this->createStub(Command::class);
		$expected = new AsyncExecutionJob($command);

		$manager = new TestJobManager($expected);

		$manager->executeAsync($command);
	}

	public function testItCreatesAnAsyncDispatchJobForADomainEvent() {
		$event = $this->createStub(DomainEvent::class);
		$expected = new AsyncDispatchJob($event);

		$manager = new TestJobManager($expected);

		$manager->dispatchAsync($event);
	}
}
