<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCanonicalUrlSet;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;
use stdClass;

#[AllowMockObjectsWithoutExpectations]
final class ContentProjectionTest extends DataTestBase {
	private SerializationService $serde;

	protected function setUp(): void {
		parent::setUp();
		$this->serde = $this->app->container->get(SerializationService::class);
	}

	public function testContentList() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$site1 = $this->randomId();
		$site2 = $this->randomId();
		$userA = $this->randomId();
		$userB = $this->randomId();

		$site1userA = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site1,
			userId: $userA,
			extensions: [
				new Tags(['test']),
			],
		);
		$site1userB = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site1,
			userId: $userB,
			extensions: [
				new Tags(['test']),
			],
		);
		$site2userA = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site2,
			userId: $userA,
			extensions: [
				new Tags(['test']),
			],
		);
		$site2userB = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $site2,
			userId: $userB,
			extensions: [
				new Tags(['test']),
			],
		);

		foreach ([$site1userA, $site1userB, $site2userA, $site2userB] as $toInsert) {
			$db->insert($env->tableName('content'), [
				'content_uuid' => $toInsert->id,
				'site_uuid' => $toInsert->siteId,
				'user_uuid' => $toInsert->userId,
				'content_obj' => $this->serde->toJson($toInsert),
			]);
			$this->assertValueObjectEquals($toInsert, $projection->contentById($toInsert->id));
		}

		$this->assertJsonStringEqualsJsonString(
			json_encode([$site1userB, $site1userA]),
			json_encode($projection->contentList(forSite: $site1)),
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site1userA]),
			json_encode($projection->contentList(forSite: $site1, ownedByUser: $userA)),
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site1userB]),
			json_encode($projection->contentList(forSite: $site1, ownedByUser: $userB)),
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site2userB, $site2userA]),
			json_encode($projection->contentList(forSite: $site2)),
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site2userA]),
			json_encode($projection->contentList(forSite: $site2, ownedByUser: $userA)),
		);
		$this->assertJsonStringEqualsJsonString(
			json_encode([$site2userB]),
			json_encode($projection->contentList(forSite: $site2, ownedByUser: $userB)),
		);
		$this->assertEmpty($projection->contentList(forSite: $this->randomId()));
		$this->assertEmpty($projection->contentList(forSite: $site1, ownedByUser: $this->randomId()));
		$this->assertEmpty($projection->contentList(forSite: $site2, ownedByUser: $this->randomId()));
	}

	public function testContentCreated() {
		$projection = $this->app->container->get(ContentProjection::class);

		$content = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *is* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				new Tags(['test']),
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
		$this->assertValueObjectEquals($content, $projection->contentById($content->id));
	}

	public function testContentUpdated() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$oldContent = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				new Tags(['tops']),
			],
		);
		$newContent = $oldContent->with(
			body: new Note(new Markdown('This *is* a test.')),
			extensions: [
				new Tags(['test']),
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
			'content_obj' => $this->serde->toJson($oldContent),
		]);
		$this->assertValueObjectEquals($oldContent, $projection->contentById($oldContent->id));

		$this->app->dispatch($event);
		$this->assertValueObjectEquals($newContent, $projection->contentById($oldContent->id));
	}

	public function testContentDeleted() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$content = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				new Tags(['tops']),
			],
		);
		$db->insert($env->tableName('content'), [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'user_uuid' => $content->userId,
			'content_obj' => $this->serde->toJson($content),
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
			id: $this->randomId(),
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				new Tags(['tops']),
			],
		);
		$db->insert($env->tableName('content'), [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'user_uuid' => $content->userId,
			'content_obj' => $this->serde->toJson($content),
		]);
		$this->assertValueObjectEquals($content, $projection->contentById($content->id));

		$event = new ContentCanonicalUrlSet(
			url: HttpMessageFactory::uri('https://test.smol.blog/note/this-was-a-test'),
			userId: $content->userId,
			aggregateId: $content->siteId,
			entityId: $content->id,
		);
		$this->app->dispatch($event);

		$this->assertValueObjectEquals(
			$content->with(canonicalUrl: HttpMessageFactory::uri('https://test.smol.blog/note/this-was-a-test')),
			$projection->contentById($content->id),
		);
	}

	public function testContentPushSucceeded() {
		$projection = $this->app->container->get(ContentProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$contentBase = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This *was* a test.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			extensions: [
				new Tags(['tops']),
			],
		);

		$entryOne = new ContentChannelEntry(
			contentId: $contentBase->id,
			channelId: $this->randomId(),
			url: HttpMessageFactory::uri('https://test.smol.blog/note/this-was-a-test'),
			details: ['wpid' => '1234'],
		);
		$contentOne = $contentBase->with(links: [$entryOne]);
		$eventOne = new ContentPushSucceeded(
			contentId: $contentBase->id,
			channelId: $entryOne->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			processId: $this->randomId(),
			url: $entryOne->url,
			details: $entryOne->details,
		);
		$this->assertValueObjectEquals($entryOne, $eventOne->getEntryObject());

		$entryTwo = new ContentChannelEntry(
			contentId: $contentBase->id,
			channelId: $this->randomId(),
			details: ['esid' => '1234'],
		);
		$contentTwo = $contentBase->with(links: [
			$entryOne,
			$entryTwo,
		]);
		$eventTwo = new ContentPushSucceeded(
			contentId: $contentBase->id,
			channelId: $entryTwo->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			processId: $this->randomId(),
			details: $entryTwo->details,
		);
		$this->assertValueObjectEquals($entryTwo, $eventTwo->getEntryObject());

		$entryThree = $entryOne->with(details: ['wpid' => '1234', 'edited' => true]);
		$contentThree = $contentBase->with(links: [
			$entryThree,
			$entryTwo,
		]);
		$eventThree = new ContentPushSucceeded(
			contentId: $contentBase->id,
			channelId: $entryThree->channelId,
			userId: $contentBase->userId,
			aggregateId: $contentBase->siteId,
			processId: $this->randomId(),
			url: $entryThree->url,
			details: $entryThree->details,
		);
		$this->assertValueObjectEquals($entryThree, $eventThree->getEntryObject());

		$db->insert($env->tableName('content'), [
			'content_uuid' => $contentBase->id,
			'site_uuid' => $contentBase->siteId,
			'user_uuid' => $contentBase->userId,
			'content_obj' => $this->serde->toJson($contentBase),
		]);
		$this->assertValueObjectEquals($contentBase, $projection->contentById($contentBase->id));

		$this->app->dispatch($eventOne);
		$this->assertValueObjectEquals($contentOne, $projection->contentById($contentBase->id));

		$this->app->dispatch($eventTwo);
		$this->assertValueObjectEquals($contentTwo, $projection->contentById($contentBase->id));

		$this->app->dispatch($eventThree);
		$this->assertValueObjectEquals($contentThree, $projection->contentById($contentBase->id));
	}

	public function testItFailsSilentlyOnEditsIfContentDoesNotExist() {
		$projection = $this->app->container->get(ContentProjection::class);

		$missingContent = new Content(
			id: $this->randomId(),
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
			),
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));

		$projection->onContentCanonicalUrlSet(
			new ContentCanonicalUrlSet(
				url: HttpMessageFactory::uri('https://smol.blog/1234'),
				aggregateId: $missingContent->siteId,
				userId: $missingContent->userId,
				entityId: $missingContent->id,
			),
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));

		$projection->onContentPushSucceeded(
			new ContentPushSucceeded(
				contentId: $missingContent->id,
				channelId: $this->randomId(),
				userId: $missingContent->userId,
				aggregateId: $missingContent->siteId,
				processId: $this->randomId(),
			),
		);
		$this->assertFalse($projection->hasContentWithId($missingContent->id));
	}
}
