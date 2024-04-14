<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Site\SiteEvent;
use Smolblog\Foundation\Value\Messages\DomainEvent;

trait SiteEventTestKit {
	public function testItWillSerializeAndDeserializeToItself() {
		$this->assertEquals($this->subject, DomainEvent::deserializeValue($this->subject->serializeValue()));
	}
}
