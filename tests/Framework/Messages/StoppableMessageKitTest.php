<?php

namespace Smolblog\Framework\Messages;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;

final class StoppableMessageKitTest extends TestCase {
	public function testItImplementsTheStoppableMessageInterface() {
		$message = new class() implements StoppableMessage { use StoppableMessageKit; };
		$this->assertTrue(in_array(StoppableMessage::class, class_implements($message)));
	}

	public function testItImplementsTheStoppableEventInterface() {
		$message = new class() implements StoppableEventInterface { use StoppableMessageKit; };
		$this->assertTrue(in_array(StoppableEventInterface::class, class_implements($message)));
	}

	public function testCallingStopMessageWillStopPropagation() {
		$message = new class() { use StoppableMessageKit; };
		$message->stopMessage();
		$this->assertTrue($message->isPropagationStopped());
	}
}
