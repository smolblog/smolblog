<?php

namespace Smolblog\Api\Exceptions;

use Smolblog\Test\TestCase;

final class ErrorResponseTest extends TestCase {
	public function testItCreatesAStandardResponse() {
		$this->assertJsonStringEqualsJsonString(
			'{"code":500, "error":"test error"}',
			json_encode(new ErrorResponse('test error'))
		);
	}
}
