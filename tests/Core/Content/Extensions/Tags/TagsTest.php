<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use PHPUnit\Framework\TestCase;

final class TagsTest extends TestCase {
	public function testNormalizationWorksCorrectly() {
		$tests = [
			'a thing that happened' => 'athingthathappened',
			'something else' => 'somethingelse',
			'somethingElse' => 'somethingelse',
			'Something Else' => 'somethingelse',
			'Something-Else' => 'somethingelse',
			'Sometimes it\'s not as easy as it looks' => 'sometimesitsnotaseasyasitlooks',
			'sometimes it Works Just Right!' => 'sometimesitworksjustright',
			'notAJoke' => 'notajoke',
			'learnToCode' => 'learntocode',
			'5FTF' => '5ftf',
			'the answer is always 42' => 'theanswerisalways42',
		];

		foreach ($tests as $actual => $expected) {
			$this->assertEquals($expected, Tags::normalizeTagText($actual));
		}
	}
}
