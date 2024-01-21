<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final readonly class TestObject extends ActivityPubBase {
	public function type(): string { return 'test'; }
}

final class ActivityPubBaseTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new TestObject(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			randomProperty: 'hullo',
		);
	}

	public function testItDelegatesTheType() {
		$this->assertEquals('test', $this->subject->type());
	}

	public function testItOnlyRequiresAnId() {
		$this->assertInstanceOf(
			ActivityPubBase::class,
			new TestObject(id: '//smol.blog/athing'),
		);
	}

	public function testAdditionalPropertiesAreAccessable() {
		$this->assertEquals('hullo', $this->subject->randomProperty);
	}

	public function testItDeserializesAnObjectWithNoTypeToNull() {
		$this->assertNull(ActivityPubBase::typedObjectFromArray(['id' => 'https://smol.blog/actor']));
	}

	public function testItDeserializesAnObjectOfUnknownTypeToNull() {
		$this->assertNull(ActivityPubBase::typedObjectFromArray(['id' => 'https://smol.blog/actor', 'type' => 'Snek']));
	}

	public function testItDeserializesAnObjectWithTypeObjectToActivityPubObject() {
		$this->assertInstanceOf(
			ActivityPubObject::class,
			ActivityPubBase::typedObjectFromArray([
				'id' => 'https://smol.blog/actor',
				'type' => 'Object',
			])
		);
	}

	public function testItDeserializesAnObjectWithAnActorTypeToActor() {
		foreach (ActorType::cases() as $type) {
			$this->assertInstanceOf(Actor::class, ActivityPubBase::typedObjectFromArray([
				'id' => 'https://smol.blog/actor',
				'type' => $type->value,
			]), "Did not deserialize ActorType $type->value correctly.");
		}
	}

	public function testItDeserializesAnObjectWithAKnownTypeToThatObject() {
		$this->assertInstanceOf(
			Note::class,
			ActivityPubBase::typedObjectFromArray([
				'id' => 'https://smol.blog/thing',
				'type' => 'Note',
			])
		);
	}
}
