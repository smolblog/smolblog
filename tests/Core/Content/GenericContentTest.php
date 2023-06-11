<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class GenericContentTest extends TestCase {
	public function testItCanBeFullyInstantiated() {
		$actual = new GenericContent(
			title: 'Hello world!',
			body: "<p>What's going on?</p>",
		);

		$this->assertInstanceOf(GenericContent::class, $actual);
		$this->assertEquals('Hello world!', $actual->getTitle());
		$this->assertEquals("<p>What's going on?</p>", $actual->getBodyContent());
		$this->assertNull($actual->typeClass);
	}

	public function testItCanBeTaggedWithTheOriginalClass() {
		$actual = new GenericContent(
			title: 'Hello world!',
			body: "<p>What's going on?</p>",
			typeClass: GenericContent::class,
		);

		$this->assertEquals(GenericContent::class, $actual->typeClass);
	}
}
