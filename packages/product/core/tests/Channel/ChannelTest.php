<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Test\TestCase;

final class ChannelTest extends TestCase {
	public function testUnknownChannelDeserialization() {
		$serialized = [
			'type' => 'oddEvan\Unknown\Channel',
			'handler' => 'testmock',
			'handlerKey' => 'same',
			'displayName' => 'Same Channel',
			'userId' => $this->randomId()->toString(),
			'connectionId' => $this->randomId()->toString(),
			'authKey' => '123',
			'anotherField' => ['one', 'two', 'three'],
		];

		$actual = Channel::deserializeValue($serialized);
		$this->assertInstanceOf(BasicChannel::class, $actual);
		$this->assertEquals('123', $actual->details['authKey']);
		$this->assertEquals(['one', 'two', 'three'], $actual->details['anotherField']);
	}
}
