<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;

final class ContentProjectionTest extends DataTestBase {
	public function testContentCreated() {
		$projection = $this->app->container->get(ContentProjection::class);

		$content = new Content(
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['test']),
			],
		);
		$event = new ContentCreated(
			body: $content->body,
			aggregateId: $content->siteId,
			userId: $content->userId,
			entityId: $content->id,
			extensions: $content->extensions,
		);

		$this->assertFalse($projection->hasContentWithId($content->id));
		$projection->onContentCreated($event);
		$this->assertTrue($projection->hasContentWithId($content->id));
		$this->assertObjectEquals($content, $projection->contentById($content->id));
	}
}
