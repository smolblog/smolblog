<?php

namespace Smolblog\Test\Kits;

trait ActivityPubActivityTestKit {
	use SerializableTestKit;

	public function testItGivesTheCorrectType() {
		$this->assertEquals($this->subject->type(), static::EXPECTED_TYPE);
	}
}
