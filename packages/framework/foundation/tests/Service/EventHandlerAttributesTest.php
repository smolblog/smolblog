<?php

namespace Smolblog\Foundation\Service;

use Crell\Tukio\ListenerPriority;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Smolblog\Test\TestCase;

#[CoversClass(Event\EventListener::class)]
#[CoversClass(Event\ProjectionListener::class)]
final class EventHandlerAttributesTest extends TestCase {
	#[TestDox('$attribute is a ListenerPriority instance.')]
	#[TestWith([Event\EventListener::class])]
	#[TestWith([Event\ProjectionListener::class])]
	public function testAttributes($attribute) {
		$this->assertInstanceOf(ListenerPriority::class, new $attribute());
	}
}
