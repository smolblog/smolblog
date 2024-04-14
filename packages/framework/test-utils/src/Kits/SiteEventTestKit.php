<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Site\SiteEvent;

trait SiteEventTestKit {
	public function testItWillSerializeAndDeserializeToItself() {
		$this->assertEquals($this->subject, SiteEvent::fromTypedArray($this->subject->serializeValue()));
	}
}
