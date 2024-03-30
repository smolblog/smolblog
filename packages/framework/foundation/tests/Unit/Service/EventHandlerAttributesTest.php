<?php

namespace Smolblog\Foundation\Service\Messaging;

use Crell\Tukio\ListenerPriority;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(CheckMemoListener::class)]
#[CoversClass(DownstreamListener::class)]
#[CoversClass(ExecutionListener::class)]
#[CoversClass(PersistEventListener::class)]
#[CoversClass(SaveMemoListener::class)]
#[CoversClass(SecurityListener::class)]
#[CoversClass(ValidateEventListener::class)]
final class EventHandlerAttributesTest extends TestCase {
	#[TestDox('$attribute is a ListenerPriority instance.')]
	#[TestWith([CheckMemoListener::class])]
	#[TestWith([DownstreamListener::class])]
	#[TestWith([ExecutionListener::class])]
	#[TestWith([PersistEventListener::class])]
	#[TestWith([SaveMemoListener::class])]
	#[TestWith([SecurityListener::class])]
	#[TestWith([ValidateEventListener::class])]
	public function testAttributes($attribute) {
		$this->assertInstanceOf(ListenerPriority::class, new $attribute());
	}
}
