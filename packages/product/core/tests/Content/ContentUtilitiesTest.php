<?php

namespace Smolblog\Core\Content;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(ContentUtilities::class)]
final class ContentUtilitiesTest extends TestCase {
	#[TestDox('::truncateText will pass through text under the limit')]
	public function testTruncateTextShortText() {
		$text = 'Eating a sandwich.';
		$this->assertEquals($text, ContentUtilities::truncateText($text, 25));
	}

	#[TestDox('::truncateText will shorten text over the limit at a word break')]
	public function testTruncateTextLongText() {
		$text = 'This is a test of the emergency broadcast system. This is only a test.';
		$this->assertEquals('This is a test of the emergency...', ContentUtilities::truncateText($text, 38));
	}
}
