<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Fields\Identifier;

final class GenericContentTest extends TestCase {
	public function testItCanBeFullyInstantiated() {
		$actual = new GenericContent(
			title: 'Hello world!',
			body: "<p>What's going on?</p>",
		);

		$this->assertInstanceOf(GenericContent::class, $actual);
		$this->assertEquals('Hello world!', $actual->getTitle());
		$this->assertEquals("<p>What's going on?</p>", $actual->getBodyContent());
		$this->assertNull($actual->originalTypeKey);
	}

	public function testItCanBeTaggedWithTheOriginalClass() {
		$actual = new GenericContent(
			title: 'Hello world!',
			body: "<p>What's going on?</p>",
			originalTypeKey: 'taters',
		);

		$this->assertEquals('taters', $actual->originalTypeKey);
	}

	public function testItsTypeKeyIsAlwaysContent() {
		$actual = new GenericContent('a', 'b');
		$this->assertEquals('content', $actual->getTypeKey());
		$this->assertNull($actual->originalTypeKey);
	}
}
