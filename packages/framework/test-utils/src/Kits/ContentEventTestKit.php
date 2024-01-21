<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Content\Events\ContentEvent;

trait ContentEventTestKit {
	public function testItWillSerializeAndDeserializeToItself() {
		$this->assertEquals($this->subject, ContentEvent::fromTypedArray($this->subject->toArray()));
	}
}
