<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use PHPUnit\Framework\Attributes\Depends;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;
use stdClass;

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
		$this->assertNull($projection->contentById($content->id));
		$this->app->dispatch($event);
		$this->assertTrue($projection->hasContentWithId($content->id));
		$this->assertObjectEquals($content, $projection->contentById($content->id) ?? new stdClass());
	}

	public function testContentUpdated() {
		$projection = $this->app->container->get(ContentProjection::class);
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();

		$oldContent = new Content(
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['tops']),
			],
		);
		$newContent = $oldContent->with(
			body: new Note(new Markdown('This *is* a test.')),
			extensions: [
				'tags' => new Tags(['test']),
			],
		);
		$event = new ContentUpdated(
			body: $newContent->body,
			aggregateId: $oldContent->siteId,
			userId: $oldContent->userId,
			entityId: $oldContent->id,
			extensions: $newContent->extensions,
		);

		$db->insert('content', [
			'content_uuid' => $oldContent->id,
			'site_uuid' => $oldContent->siteId,
			'content_obj' => json_encode($oldContent),
		]);
		$this->assertObjectEquals($oldContent, $projection->contentById($oldContent->id) ?? new stdClass());

		$this->app->dispatch($event);
		$this->assertObjectEquals($newContent, $projection->contentById($oldContent->id) ?? new stdClass());
	}
}
