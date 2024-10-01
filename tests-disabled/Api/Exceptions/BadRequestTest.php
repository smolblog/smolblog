<?php

namespace Smolblog\Api\Exceptions;

use Smolblog\Test\TestCase;

final class BadRequestTest extends TestCase {
	public function testItCreatesAStandardResponse() {
		$this->assertJsonStringEqualsJsonString(
			'{"code":400, "error":"test error"}',
			json_encode(new BadRequest('test error'))
		);
	}
}
