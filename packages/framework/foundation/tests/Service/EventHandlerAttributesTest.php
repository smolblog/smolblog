<?php

namespace Smolblog\Foundation\Service;

use Crell\Tukio\ListenerPriority;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Smolblog\Test\TestCase;

#[CoversClass(Command\CommandHandler::class)]
#[CoversClass(Event\EventListener::class)]
#[CoversClass(Event\ProjectionListener::class)]
#[CoversClass(Event\ValidationListener::class)]
#[CoversClass(Query\QueryHandler::class)]
final class EventHandlerAttributesTest extends TestCase {
	#[TestDox('$attribute is a ListenerPriority instance.')]
	#[TestWith([Command\CommandHandler::class])]
	#[TestWith([Event\EventListener::class])]
	#[TestWith([Event\ProjectionListener::class])]
	#[TestWith([Event\ValidationListener::class])]
	#[TestWith([Query\QueryHandler::class])]
	public function testAttributes($attribute) {
		$this->assertInstanceOf(ListenerPriority::class, new $attribute());
	}
}
