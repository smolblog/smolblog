<?php

namespace Smolblog\Core\ContentV1\Extensions\Tags;

use Smolblog\Test\TestCase;

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

	public function testItCanBeConstructedWithTags() {
		$expected = [
			new Tag('tag one'),
			new Tag('Tag2'),
		];

		$tags = new Tags(tags: $expected);
		$this->assertEquals($expected, $tags->tags);
	}

	public function testItWillSerializeCorrectly() {
		$expected = [ 'tags' => [
			['text' => 'tag one', 'normalized' => Tags::normalizeTagText('tag one')],
			['text' => 'Tag2', 'normalized' => Tags::normalizeTagText('Tag2')],
		]];

		$actual = new Tags(tags: [
			new Tag('tag one'),
			new Tag('Tag2'),
		]);

		$this->assertEquals($expected, $actual->toArray());
	}

	public function testItWillDeserializeCorrectly() {
		$actual = [ 'tags' => [
			['text' => 'tag one', 'normalized' => Tags::normalizeTagText('tag one')],
			['text' => 'Tag2', 'normalized' => Tags::normalizeTagText('Tag2')],
		]];

		$expected = new Tags(tags: [
			new Tag('tag one'),
			new Tag('Tag2'),
		]);

		$this->assertEquals($expected, Tags::fromArray($actual));
	}
}
