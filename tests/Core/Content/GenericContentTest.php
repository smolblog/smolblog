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
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
		);

		$this->assertInstanceOf(Content::class, $actual);
		$this->assertEquals('Hello world!', $actual->getTitle());
		$this->assertEquals("<p>What's going on?</p>", $actual->getBodyContent());
	}
}
