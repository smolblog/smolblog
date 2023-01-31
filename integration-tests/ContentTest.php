<?php

namespace Smolblog\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentVisibilityChanged;
use Smolblog\Core\Content\Types\Status\Status;
use Smolblog\Core\Content\Types\Status\StatusBodyEdited;
use Smolblog\Core\Content\Types\Status\StatusById;
use Smolblog\Core\Content\Types\Status\StatusCreated;
use Smolblog\Framework\Infrastructure\QueryMemoizationService;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Mock\App;

final class ContentTest extends TestCase {
	public function testStatusContentEvents() {
		$contentId = Identifier::createRandom();
		$userId = Identifier::createRandom();
		$siteId = Identifier::createRandom();

		App::dispatch(new StatusCreated(
			text: 'Hello everybody!',
			authorId: $userId,
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			publishTimestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
			permalink: '/status/' . $contentId->toString(),
		));

		$this->assertEquals(
			new Status(
				text: 'Hello everybody!',
				authorId: $userId,
				id: $contentId,
				siteId: $siteId,
				publishTimestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
				permalink: '/status/' . $contentId->toString(),
				visibility: ContentVisibility::Draft,
			),
			App::fetch(new StatusById($contentId))
		);

		App::dispatch(new StatusBodyEdited(
			text: 'Hello everybody! Except @oddEvan. Screw that guy.',
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::dispatch(new ContentBaseAttributeEdited(
			permalink: '/status/hello-everybody',
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::dispatch(new ContentVisibilityChanged(
			visibility: ContentVisibility::Published,
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::getService(QueryMemoizationService::class)->reset();
		$this->assertEquals(
			new Status(
				text: 'Hello everybody! Except @oddEvan. Screw that guy.',
				authorId: $userId,
				id: $contentId,
				siteId: $siteId,
				publishTimestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
				permalink: '/status/hello-everybody',
				visibility: ContentVisibility::Published,
			),
			App::fetch(new StatusById($contentId))
		);

		App::dispatch(new ContentDeleted(
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::getService(QueryMemoizationService::class)->reset();
		$this->assertNull(App::fetch(new StatusById($contentId)));
	}
}
