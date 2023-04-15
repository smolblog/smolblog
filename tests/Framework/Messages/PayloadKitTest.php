<?php

namespace Smolblog\Framework\Messages;

use DateTimeInterface;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

abstract class TestBaseEvent extends Event {
	use PayloadKit;
	public function __construct(public readonly string $mainProp, ...$parentProps) {
		parent::__construct(...$parentProps);
	}
	private function getStandardProperties(): array { return ['mainProp' => $this->mainProp]; }
}

final class TestSubEventWithId extends TestBaseEvent {
	public function __construct(public readonly Identifier $someId, ...$parentProps) {
		parent::__construct(...$parentProps);
	}
	public function getPayload(): array { return ['someId' => strval($this->someId)]; }
	protected static function payloadFromArray(array $payload): array {
		return ['someId' => Identifier::fromString($payload['someId'])];
	}
}

final class TestSubEvent extends TestBaseEvent {
	public function __construct(public readonly string $subProp, ...$parentProps) {
		parent::__construct(...$parentProps);
	}
	public function getPayload(): array { return ['subProp' => $this->subProp]; }
}

final class PayloadKitTest extends TestCase {
	public function testDifferentSubEventsWillSerializeToSameFormat() {
		$expectedKeys = ['type', 'id', 'timestamp', 'mainProp', 'payload'];
		$someId = $this->randomId();

		$eventOne = new TestSubEvent(subProp: 'hello', mainProp: 'everybody');
		$arrayOne = $eventOne->toArray();
		$eventTwo = new TestSubEventWithId(someId: $someId, mainProp: 'everybody');
		$arrayTwo = $eventTwo->toArray();

		$this->assertEquals($expectedKeys, array_keys($arrayOne));
		$this->assertEquals(TestSubEvent::class, $arrayOne['type']);
		$this->assertEquals('everybody', $arrayOne['mainProp']);
		$this->assertEquals('hello', $arrayOne['payload']['subProp']);

		$this->assertEquals($expectedKeys, array_keys($arrayTwo));
		$this->assertEquals(TestSubEventWithId::class, $arrayTwo['type']);
		$this->assertEquals('everybody', $arrayTwo['mainProp']);
		$this->assertEquals(strval($someId), $arrayTwo['payload']['someId']);
	}

	public function testSubEventWithPrimitivePropertiesWillUnserialize() {
		$serialized = [
			'type' => __NAMESPACE__ . '\TestSubEvent',
			'id' => 'b9d3f44d-c0a9-4efa-ab76-ffe3baeb8aca',
			'timestamp' => '2005-08-15T15:52:01.000+00:00',
			'mainProp' => 'this is',
			'payload' => ['subProp' => 'a website'],
		];
		$actual = TestBaseEvent::fromTypedArray($serialized);

		$this->assertInstanceOf(TestSubEvent::class, $actual);
		$this->assertInstanceOf(Identifier::class, $actual->id);
		$this->assertEquals('b9d3f44d-c0a9-4efa-ab76-ffe3baeb8aca', strval($actual->id));
		$this->assertEquals('this is', $actual->mainProp);
		$this->assertEquals('a website', $actual->subProp);
	}

	public function testSubEventWithObjectPropertiesWillUnserialize() {
		$serialized = [
			'type' => __NAMESPACE__ . '\TestSubEventWithId',
			'id' => '43771c95-6f1e-4c86-bff9-eba45e4c79e6',
			'timestamp' => '2005-08-15T15:52:01.000+00:00',
			'mainProp' => 'it\'s dot com',
			'payload' => ['someId' => '14faea27-6cdb-4a9c-b6b5-5df6cb4448c1'],
		];
		$actual = TestBaseEvent::fromTypedArray($serialized);

		$this->assertInstanceOf(TestSubEventWithId::class, $actual);
		$this->assertInstanceOf(Identifier::class, $actual->id);
		$this->assertEquals('43771c95-6f1e-4c86-bff9-eba45e4c79e6', strval($actual->id));
		$this->assertEquals('it\'s dot com', $actual->mainProp);
		$this->assertInstanceOf(Identifier::class, $actual->someId);
		$this->assertEquals('14faea27-6cdb-4a9c-b6b5-5df6cb4448c1', strval($actual->someId));
	}
}
