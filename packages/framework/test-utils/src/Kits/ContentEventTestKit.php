<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\ContentV1\Events\ContentEvent;

trait ContentEventTestKit {
	public function testItWillSerializeAndDeserializeToItself() {
		$this->assertEquals($this->subject, ContentEvent::fromTypedArray($this->subject->serializeValue()));
	}
}
