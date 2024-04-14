<?php

namespace Smolblog\Test\Kits;

trait SerializableTestKit {
	public function testItWillSerializeToArrayAndDeserializeToItself() {
		$class = get_class($this->subject);
		$this->assertEquals($this->subject, $class::deserializeValue($this->subject->serializeValue()));
	}

	public function testItWillSerializeToJsonAndDeserializeToItself() {
		$class = get_class($this->subject);
		$this->assertEquals($this->subject, $class::jsonDeserialize(json_encode($this->subject)));
	}
}
