<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCanonicalUrlSet;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\Foundation\Value\Fields\Url;
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

	public function testContentDeleted() {
		$projection = $this->app->container->get(ContentProjection::class);
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();

		$content = new Content(
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['tops']),
			],
		);
		$db->insert('content', [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'content_obj' => json_encode($content),
		]);
		$this->assertTrue($projection->hasContentWithId($content->id));

		$event = new ContentDeleted(
			userId: $content->userId,
			aggregateId: $content->siteId,
			entityId: $content->id,
		);
		$this->app->dispatch($event);
		$this->assertFalse($projection->hasContentWithId($content->id));
	}

	public function testContentCanonicalUrlSet() {
		$projection = $this->app->container->get(ContentProjection::class);
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();

		$content = new Content(
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['tops']),
			],
		);
		$db->insert('content', [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'content_obj' => json_encode($content),
		]);
		$this->assertObjectEquals($content, $projection->contentById($content->id) ?? new stdClass());

		$event = new ContentCanonicalUrlSet(
			url: new Url('https://test.smol.blog/note/this-was-a-test'),
			userId: $content->userId,
			aggregateId: $content->siteId,
			entityId: $content->id,
		);
		$this->app->dispatch($event);

		$this->assertObjectEquals(
			$content->with(canonicalUrl: new Url('https://test.smol.blog/note/this-was-a-test')),
			$projection->contentById($content->id) ?? new stdClass()
		);
	}

	public function testContentPushedToChannel() {
		$projection = $this->app->container->get(ContentProjection::class);
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();

		$contentBase = new Content(
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['tops']),
			],
		);

		$entryOne = new ContentChannelEntry(
			contentId: $contentBase->id,
			channelId: $this->randomId(),
			url: new Url('https://test.smol.blog/note/this-was-a-test'),
			details: ['wpid' => '1234'],
		);
		$contentOne = $contentBase->with(links: [$entryOne->getId()->toString() => $entryOne]);
		$eventOne = new ContentPushedToChannel(
			content: $contentBase,
			channelId: $entryOne->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			url: $entryOne->url,
			details: $entryOne->details,
		);

		$entryTwo = new ContentChannelEntry(
			contentId: $contentBase->id,
			channelId: $this->randomId(),
			details: ['esid' => '1234'],
		);
		$contentTwo = $contentBase->with(links: [
			$entryOne->getId()->toString() => $entryOne,
			$entryTwo->getId()->toString() => $entryTwo,
		]);
		$eventTwo = new ContentPushedToChannel(
			content: $contentOne,
			channelId: $entryTwo->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			details: $entryTwo->details,
		);

		$entryThree = $entryOne->with(details: ['wpid' => '1234', 'edited' => true]);
		$contentThree = $contentBase->with(links: [
			$entryOne->getId()->toString() => $entryThree,
			$entryTwo->getId()->toString() => $entryTwo,
		]);
		$eventThree = new ContentPushedToChannel(
			content: $contentTwo,
			channelId: $entryThree->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			url: $entryThree->url,
			details: $entryThree->details,
		);

		$db->insert('content', [
			'content_uuid' => $contentBase->id,
			'site_uuid' => $contentBase->siteId,
			'content_obj' => json_encode($contentBase),
		]);
		$this->assertObjectEquals($contentBase, $projection->contentById($contentBase->id) ?? new stdClass());

		$this->app->dispatch($eventOne);
		$this->assertObjectEquals($contentOne, $projection->contentById($contentBase->id) ?? new stdClass());

		$this->app->dispatch($eventTwo);
		$this->assertObjectEquals($contentTwo, $projection->contentById($contentBase->id) ?? new stdClass());

		$this->app->dispatch($eventThree);
		$this->assertObjectEquals($contentThree, $projection->contentById($contentBase->id) ?? new stdClass());
	}

	public function testItFailsSilentlyOnEditsIfContentDoesNotExist() {
		$projection = $this->app->container->get(ContentProjection::class);

		$missingContent = new Content(
			body: new Note(text: new Markdown('Is that a crab with a top hat and a monocle?')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));

		$projection->onContentUpdated(
			new ContentUpdated(
				body: $missingContent->body,
				aggregateId: $missingContent->siteId,
				userId: $missingContent->userId,
				entityId: $missingContent->id,
			)
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));

		$projection->onContentCanonicalUrlSet(
			new ContentCanonicalUrlSet(
				url: new Url('https://smol.blog/1234'),
				aggregateId: $missingContent->siteId,
				userId: $missingContent->userId,
				entityId: $missingContent->id,
			)
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));

		$projection->onContentPushedToChannel(
			new ContentPushedToChannel(
				content: $missingContent,
				channelId: $this->randomId(),
				userId: $missingContent->userId,
				aggregateId: $missingContent->siteId,
			)
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));
	}
}
