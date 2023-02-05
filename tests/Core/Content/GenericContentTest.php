<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class GenericContentTest extends TestCase {
	public function testItCanBeFullyInstantiated() {
		$actual = new GenericContent(
			title: 'Hello world!',
			body: "<p>What's going on?</p>",
			siteId: Identifier::createRandom(),
			authorId: Identifier::createRandom(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: Identifier::createRandom(),
		);

		$this->assertInstanceOf(Content::class, $actual);
		$this->assertEquals('Hello world!', $actual->getTitle());
		$this->assertEquals("<p>What's going on?</p>", $actual->getBodyContent());
	}
}
