<?php

namespace Smolblog\Core\Content\Queries;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\SerializableKit;

final class TestExtensionForQuery implements ContentExtension {
	use SerializableKit;
	public function __construct(public readonly string $tagline) {}
}

final class ExtensableContentQueryKitTest extends TestCase {
	public function testItSetsAnExtensionOnTheContent() {
		$query = new class() extends Query implements ExtensableContentQuery {
			use ExtensableContentQueryKit;
		};
		$query->results = new GenericContent(
			title: 'Content Test',
			body: '<p>A thing</p>',
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
		);

		$query->setExtension(new TestExtensionForQuery(tagline: 'boom'));
		$actual = $query->results->getExtension(TestExtensionForQuery::class);

		$this->assertInstanceOf(TestExtensionForQuery::class, $actual);
		$this->assertEquals('boom', $actual->tagline);
	}
}
