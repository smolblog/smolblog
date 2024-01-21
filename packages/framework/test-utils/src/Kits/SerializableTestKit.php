<?php

namespace Smolblog\Test\Kits;

trait SerializableTestKit {
	public function testItWillSerializeToArrayAndDeserializeToItself() {
		$class = get_class($this->subject);
		$this->assertEquals($this->subject, $class::fromArray($this->subject->toArray()));
	}

	public function testItWillSerializeToJsonAndDeserializeToItself() {
		$class = get_class($this->subject);
		$this->assertEquals($this->subject, $class::jsonDeserialize(json_encode($this->subject)));
	}
}
