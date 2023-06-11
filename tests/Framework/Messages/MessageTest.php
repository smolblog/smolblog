<?php

namespace Smolblog\Framework\Messages;

use Smolblog\Test\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;

final class MessageTest extends TestCase {
	public function testItImplementsTheStoppableEventInterface() {
		$message = new class() extends Message {};
		$this->assertTrue(in_array(StoppableEventInterface::class, class_implements($message)));
	}

	public function testCallingStopMessageWillStopPropagation() {
		$message = new class() extends Message {};
		$message->stopMessage();
		$this->assertTrue($message->isPropagationStopped());
	}
}
