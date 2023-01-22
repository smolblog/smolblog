<?php

namespace Smolblog\Core\Connector\Entities;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class AuthRequestStateTest extends TestCase {
	public function testItSerializesToJsonCorrectly() {
		$state = new AuthRequestState(
			key: 'b9b9f013-e948-4d52-8bba-9cfff4fff640',
			userId: Identifier::fromString('16d9e789-5179-4df9-920d-03246aa22e98'),
			info: ['smol' => 'blog', 'key' => 'value'],
		);
		$expected = <<<EOF
{
	"key": "b9b9f013-e948-4d52-8bba-9cfff4fff640",
	"userId": "16d9e789-5179-4df9-920d-03246aa22e98",
	"info": { "smol": "blog", "key": "value" }
}
EOF;

		$this->assertJsonStringEqualsJsonString($expected, json_encode($state));
	}

	public function testItDeserializesFromJsonCorrectly() {
		$expected = new AuthRequestState(
			key: 'b9b9f013-e948-4d52-8bba-9cfff4fff640',
			userId: Identifier::fromString('16d9e789-5179-4df9-920d-03246aa22e98'),
			info: ['smol' => 'blog', 'key' => 'value'],
		);
		$json = <<<EOF
{
	"key": "b9b9f013-e948-4d52-8bba-9cfff4fff640",
	"userId": "16d9e789-5179-4df9-920d-03246aa22e98",
	"info": { "smol": "blog", "key": "value" }
}
EOF;

		$this->assertEquals($expected, AuthRequestState::jsonDeserialize($json));
	}
}
