<?php

namespace Smolblog\Test;

use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Constraints\DomainEventChecker;

final readonly class TestDomainEvent extends DomainEvent {}

final class DomainEventCheckerTest extends TestCase {
	public function testItChecksEventsExcludingIdAndTimestamp() {
		$data = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
			'processId' => $this->randomId(),
		];
		$one = new TestDomainEvent(...$data);
		$two = new TestDomainEvent(...$data);

		$this->assertNotEquals($one->id, $two->id);
		$this->assertNotEquals($one->timestamp, $two->timestamp);
		$this->assertThat($one, new DomainEventChecker([$two]));
	}
	public function testItFailsEventsWhereOtherFieldsDoNotMatch() {
		$base = new TestDomainEvent(
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
			entityId: $this->randomId(),
			processId: $this->randomId(),
		);

		$this->assertThat($base, new DomainEventChecker([$base]));
		$this->assertThat($base, $this->logicalNot(new DomainEventChecker([$base->with(userId: $this->randomId())])));
		$this->assertThat($base, $this->logicalNot(new DomainEventChecker([$base->with(aggregateId: $this->randomId())])));
		$this->assertThat($base, $this->logicalNot(new DomainEventChecker([$base->with(entityId: $this->randomId())])));
		$this->assertThat($base, $this->logicalNot(new DomainEventChecker([$base->with(processId: $this->randomId())])));
	}

	public function testItChecksMultipleEventsInOrder() {
		$data1 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
			'processId' => $this->randomId(),
		];
		$oneRef = new TestDomainEvent(...$data1);
		$oneCheck = new TestDomainEvent(...$data1);

		$data2 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
			'processId' => $this->randomId(),
		];
		$twoRef = new TestDomainEvent(...$data2);
		$twoCheck = new TestDomainEvent(...$data2);

		$checker = new DomainEventChecker([$oneRef, $twoRef]);
		$this->assertThat($oneCheck, $checker);
		$this->assertThat($twoCheck, $checker);
	}

	public function testItFailsEventsOutOfOrder() {
		$data1 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
			'processId' => $this->randomId(),
		];
		$oneRef = new TestDomainEvent(...$data1);
		$oneCheck = new TestDomainEvent(...$data1);

		$data2 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
			'processId' => $this->randomId(),
		];
		$twoRef = new TestDomainEvent(...$data2);
		$twoCheck = new TestDomainEvent(...$data2);

		$checker = new DomainEventChecker([$oneRef, $twoRef]);
		$this->assertThat($twoCheck, $this->logicalNot($checker));
		$this->assertThat($oneCheck, $this->logicalNot($checker));
	}

	public function testItCanCheckForAConsistentProcessId() {
		$processId = $this->randomId();
		$data1 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
		];
		$oneRef = new TestDomainEvent(...$data1);
		$oneCheck = new TestDomainEvent(...$data1, processId: $processId);

		$data2 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
		];
		$twoRef = new TestDomainEvent(...$data2);
		$twoCheck = new TestDomainEvent(...$data2, processId: $processId);

		$checker = new DomainEventChecker([$oneRef, $twoRef], checkProcess: true);
		$this->assertThat($oneCheck, $checker);
		$this->assertThat($twoCheck, $checker);
	}

	public function testItCanFailAnInconsistentProcessId() {
		$data1 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
		];
		$oneRef = new TestDomainEvent(...$data1);
		$oneCheck = new TestDomainEvent(...$data1, processId: $this->randomId());

		$data2 = [
			'userId' => $this->randomId(),
			'aggregateId' => $this->randomId(),
			'entityId' => $this->randomId(),
		];
		$twoRef = new TestDomainEvent(...$data2);
		$twoCheck = new TestDomainEvent(...$data2, processId: $this->randomId());

		$checker = new DomainEventChecker([$oneRef, $twoRef], checkProcess: true);
		$this->assertThat($oneCheck, $checker);
		$this->assertThat($twoCheck, $this->logicalNot($checker));
	}
}
