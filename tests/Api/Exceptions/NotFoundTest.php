<?php

namespace Smolblog\Api\Exceptions;

use PHPUnit\Framework\TestCase;

final class NotFoundTest extends TestCase {
	public function testItCreatesAStandardResponse() {
		$this->assertJsonStringEqualsJsonString(
			'{"code":404, "error":"test error"}',
			json_encode(new NotFound('test error'))
		);
	}
}
