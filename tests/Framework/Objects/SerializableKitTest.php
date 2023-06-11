<?php

namespace Smolblog\Framework\Objects;

use DateTimeImmutable;
use JsonSerializable;
use Smolblog\Test\TestCase;

final class TestSerializableObject { use SerializableKit; }

final class SerializableKitTest extends TestCase {
	public function testItImplementsJsonSerializable() {
		$thing = new class() implements JsonSerializable { use SerializableKit; };
		$this->assertTrue(in_array(JsonSerializable::class, class_implements($thing)));
	}

	public function testItImplementsArraySerializable() {
		$thing = new class() implements ArraySerializable { use SerializableKit; };
		$this->assertTrue(in_array(ArraySerializable::class, class_implements($thing)));
	}

	public function testItWillDeserializeAnIdentifierOrFailSilently() {
		$goodId = TestSerializableObject::safeDeserializeIdentifier('010e2d7f-2df3-4dd6-9325-f57735ef1ecc');
		$this->assertInstanceOf(Identifier::class, $goodId);
		$this->assertEquals('010e2d7f-2df3-4dd6-9325-f57735ef1ecc', strval($goodId));

		$this->assertNull(TestSerializableObject::safeDeserializeIdentifier(''));
		$this->assertNull(TestSerializableObject::safeDeserializeIdentifier('Not an ID at all.'));
	}

	public function testItWillDeserializeADateOrFailSilently() {
		$goodDate = TestSerializableObject::safeDeserializeDate('2022-02-22');
		$this->assertInstanceOf(DateTimeImmutable::class, $goodDate);
		$this->assertEquals('February 22, 2022', $goodDate->format('F j, Y'));

		$this->assertNull(TestSerializableObject::safeDeserializeDate(''));
		$this->assertNull(TestSerializableObject::safeDeserializeDate('Not a date at all.'));
	}
}
