<?php

namespace Smolblog\Test\Kits;
use PHPUnit\Framework\MockObject\MockObject;

trait MessageBusMockKit {
	/** @see https://stackoverflow.com/a/76110856 */
	private function messageBusShouldDispatch(MockObject $bus, mixed ...$constraints) {
		$matcher = $this->exactly(count($constraints));
		$bus->
			expects($matcher)->
			method('dispatch')->
			with($this->callback(
				fn($param) => $this->assertThat($param, $constraints[$matcher->numberOfInvocations() - 1]) || true
			));
	}

	private function messageBusShouldDispatchAsync(MockObject $bus, mixed ...$constraints) {
		$matcher = $this->exactly(count($constraints));
		$bus->
			expects($matcher)->
			method('dispatchAsync')->
			with($this->callback(
				fn($param) => $this->assertThat($param, $constraints[$matcher->numberOfInvocations() - 1]) || true
			));
	}
}
