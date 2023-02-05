<?php

namespace Smolblog\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Types\Status\CreateStatus;
use Smolblog\Core\Content\Types\Status\DeleteStatus;
use Smolblog\Core\Content\Types\Status\EditStatus;
use Smolblog\Core\Content\Types\Status\Status;
use Smolblog\Core\Content\Types\Status\StatusById;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Mock\App;
use Smolblog\Mock\MockMemoService;
use Smolblog\Mock\SecurityService;

final class ContentTest extends TestCase {
	public function testStatusLifecycle() {
		$userId = Identifier::fromString(SecurityService::SITE1AUTHOR);
		$siteId = Identifier::fromString(SecurityService::SITE1);

		$createCommand = new CreateStatus(
			siteId: $siteId,
			userId: $userId,
			text: 'Hello everybody!'
		);
		App::dispatch($createCommand);

		$contentId = $createCommand->statusId;
		$content = App::fetch(new StatusById($contentId));

		$this->assertInstanceOf(Status::class, $content);
		$this->assertEquals("<p>Hello everybody!</p>\n", $content->getBodyContent());

		App::dispatch(new EditStatus(
			text: 'Hello everybody! Except @oddEvan. Screw that guy.',
			statusId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::getService(MockMemoService::class)->reset();
		$this->assertEquals(
			new Status(
				text: 'Hello everybody! Except @oddEvan. Screw that guy.',
				authorId: $userId,
				id: $contentId,
				siteId: $siteId,
				publishTimestamp: $content->publishTimestamp,
				permalink: $content->permalink,
				visibility: ContentVisibility::Published,
				rendered: "<p>Hello everybody! Except @oddEvan. Screw that guy.</p>\n"
			),
			App::fetch(new StatusById($contentId))
		);

		App::dispatch(new DeleteStatus(
			statusId: $contentId,
			userId: $userId,
			siteId: $siteId,
		));

		App::getService(MockMemoService::class)->reset();
		$this->assertNull(App::fetch(new StatusById($contentId)));
	}
}
