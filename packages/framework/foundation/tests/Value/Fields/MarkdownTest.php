<?php

namespace Smolblog\Foundation\Value\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(Markdown::class)]
final class MarkdownTest extends TestCase {
	#[TestDox('It stores a markdown string (which is any string really)')]
	public function testRandom() {
		$this->assertInstanceOf(Markdown::class, new Markdown('My email is <snek@smol.blog>.'));
	}

	#[TestDox('It will serialize to and deserialize from a string. Because it is one.')]
	public function testSerialization() {
		$markdownString = 'All your base are belong to us.';
		$markdownObject = new Markdown('All your base are belong to us.');

		$this->assertEquals($markdownString, $markdownObject->toString());
		$this->assertEquals(strval($markdownObject), strval(Markdown::fromString($markdownString)));
	}
}
