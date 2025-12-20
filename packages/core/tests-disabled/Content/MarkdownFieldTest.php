<?php

namespace Smolblog\Core\Content\Fields;

use Smolblog\Test\TestCase;

final class MarkdownFieldTest extends TestCase {
	public function testItWillSerializeAndDeserializeCorrectly() {
		$string = 'This is _only_ a test.';
		$object = new Markdown('This is _only_ a test.');

		$this->assertEquals($string, $object->toString());
		$this->assertEquals($object, Markdown::fromString($string));
	}
}
