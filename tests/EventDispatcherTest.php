<?php

namespace Smolblog\Core;

use PHPUnit\Framework\TestCase;

final class EventDispatcherTestEvent {
	function __construct(public string $testString) {}
}

final class EventDispatcherTest extends TestCase {
	public function testAnEventCanBeSubscribedAndDispatched() {
		$callbackHit = false;
		$dispatcher = new EventDispatcher();
		$dispatcher->subscribeTo(
			EventDispatcherTestEvent::class,
			function($event) use(&$callbackHit) {
				$callbackHit = true;
				$this->assertEquals('camelot', $event->testString);
			}
		);

		$dispatcher->dispatch(new EventDispatcherTestEvent('camelot'));
		$this->assertTrue($callbackHit);
	}

	public function testAnEventCanBeModifiedBySubscribers() {
		$dispatcher = new EventDispatcher();
		$dispatcher->subscribeTo(
			EventDispatcherTestEvent::class,
			function($event) {
				$this->assertEquals('camelot', $event->testString);
				$event->testString = "it's only a model";
				return $event;
			}
		);

		$filtered = $dispatcher->dispatch(new EventDispatcherTestEvent('camelot'));
		$this->assertEquals("it's only a model", $filtered->testString);
	}
}
