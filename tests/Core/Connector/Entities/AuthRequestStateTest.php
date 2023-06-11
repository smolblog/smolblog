<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class AuthRequestStateTest extends TestCase {
	public function testItSerializesToJsonCorrectly() {
		$state = new AuthRequestState(
			key: 'b9b9f013-e948-4d52-8bba-9cfff4fff640',
			userId: Identifier::fromString('16d9e789-5179-4df9-920d-03246aa22e98'),
			provider: 'smolblog',
			info: ['smol' => 'blog', 'key' => 'value'],
			returnToUrl: 'https://dashboard.smolblog.com/account/connections'
		);
		$expected = <<<EOF
{
	"key": "b9b9f013-e948-4d52-8bba-9cfff4fff640",
	"userId": "16d9e789-5179-4df9-920d-03246aa22e98",
	"provider": "smolblog",
	"info": { "smol": "blog", "key": "value" },
	"returnToUrl": "https://dashboard.smolblog.com/account/connections"
}
EOF;

		$this->assertJsonStringEqualsJsonString($expected, json_encode($state));
	}

	public function testItDeserializesFromJsonCorrectly() {
		$expected = new AuthRequestState(
			key: 'b9b9f013-e948-4d52-8bba-9cfff4fff640',
			userId: Identifier::fromString('16d9e789-5179-4df9-920d-03246aa22e98'),
			provider: 'smolblog',
			info: ['smol' => 'blog', 'key' => 'value'],
			returnToUrl: 'https://dashboard.smolblog.com/account/connections',
		);
		$json = <<<EOF
{
	"key": "b9b9f013-e948-4d52-8bba-9cfff4fff640",
	"userId": "16d9e789-5179-4df9-920d-03246aa22e98",
	"provider": "smolblog",
	"info": { "smol": "blog", "key": "value" },
	"returnToUrl": "https://dashboard.smolblog.com/account/connections"
}
EOF;

		$this->assertEquals($expected, AuthRequestState::jsonDeserialize($json));
	}
}
