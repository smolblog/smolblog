<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCanonicalUrlSet;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\Foundation\Value\Fields\Url;
use stdClass;

final class ContentProjectionTest extends DataTestBase {
	public function testContentList() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$site1 = $this->randomId();
		$site2 = $this->randomId();
		$userA = $this->randomId();
		$userB = $this->randomId();

		$site1userA = new Content(
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site1,
			userId: $userA,
			extensions: [
				'tags' => new Tags(['test']),
			],
		);
		$site1userB = new Content(
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site1,
			userId: $userB,
			extensions: [
				'tags' => new Tags(['test']),
			],
		);
		$site2userA = new Content(
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site2,
			userId: $userA,
			extensions: [
				'tags' => new Tags(['test']),
			],
		);
		$site2userB = new Content(
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site2,
			userId: $userB,
			extensions: [
				'tags' => new Tags(['test']),
			],
		);

		foreach ([$site1userA, $site1userB, $site2userA, $site2userB] as $toInsert) {
			$db->insert($env->tableName('content'), [
				'content_uuid' => $toInsert->id,
				'site_uuid' => $toInsert->siteId,
				'user_uuid' => $toInsert->userId,
				'content_obj' => json_encode($toInsert),
			]);
			$this->assertObjectEquals($toInsert, $projection->contentById($toInsert->id) ?? new stdClass());
		}

		$this->assertJsonStringEqualsJsonString(
			json_encode([$site1userB, $site1userA]), json_encode($projection->contentList(forSite: $site1))
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site1userA]), json_encode($projection->contentList(forSite: $site1, ownedByUser: $userA))
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site1userB]), json_encode($projection->contentList(forSite: $site1, ownedByUser: $userB))
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site2userB, $site2userA]), json_encode($projection->contentList(forSite: $site2))
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site2userA]), json_encode($projection->contentList(forSite: $site2, ownedByUser: $userA))
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site2userB]), json_encode($projection->contentList(forSite: $site2, ownedByUser: $userB))
		);
		$this->assertEmpty($projection->contentList(forSite: $this->randomId()));
		$this->assertEmpty($projection->contentList(forSite: $site1, ownedByUser: $this->randomId()));
		$this->assertEmpty($projection->contentList(forSite: $site2, ownedByUser: $this->randomId()));
	}

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
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

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

		$db->insert($env->tableName('content'), [
			'content_uuid' => $oldContent->id,
			'site_uuid' => $oldContent->siteId,
			'user_uuid' => $oldContent->userId,
			'content_obj' => json_encode($oldContent),
		]);
		$this->assertObjectEquals($oldContent, $projection->contentById($oldContent->id) ?? new stdClass());

		$this->app->dispatch($event);
		$this->assertObjectEquals($newContent, $projection->contentById($oldContent->id) ?? new stdClass());
	}

	public function testContentDeleted() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$content = new Content(
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['tops']),
			],
		);
		$db->insert($env->tableName('content'), [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'user_uuid' => $content->userId,
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
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$content = new Content(
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				'tags' => new Tags(['tops']),
			],
		);
		$db->insert($env->tableName('content'), [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'user_uuid' => $content->userId,
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

	public function testContentPushSucceeded() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

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
		$eventOne = new ContentPushSucceeded(
			contentId: $contentBase->id,
			channelId: $entryOne->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			processId: $this->randomId(),
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
		$eventTwo = new ContentPushSucceeded(
			contentId: $contentOne->id,
			channelId: $entryTwo->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			processId: $this->randomId(),
			details: $entryTwo->details,
		);

		$entryThree = $entryOne->with(details: ['wpid' => '1234', 'edited' => true]);
		$contentThree = $contentBase->with(links: [
			$entryOne->getId()->toString() => $entryThree,
			$entryTwo->getId()->toString() => $entryTwo,
		]);
		$eventThree = new ContentPushSucceeded(
			contentId: $contentTwo->id,
			channelId: $entryThree->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			processId: $this->randomId(),
			url: $entryThree->url,
			details: $entryThree->details,
		);

		$db->insert($env->tableName('content'), [
			'content_uuid' => $contentBase->id,
			'site_uuid' => $contentBase->siteId,
			'user_uuid' => $contentBase->userId,
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

		$projection->onContentPushSucceeded(
			new ContentPushSucceeded(
				contentId: $missingContent->id,
				channelId: $this->randomId(),
				userId: $missingContent->userId,
				aggregateId: $missingContent->siteId,
				processId: $this->randomId(),
			)
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));
	}
}
