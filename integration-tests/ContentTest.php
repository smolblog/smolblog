<?php

namespace Smolblog\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Types\Status\Status;
use Smolblog\Core\Content\Types\Status\StatusCreated;
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
			publishTimestamp: new DateTimeImmutable(),
			permalink: '/status/' . $contentId->toString(),
			visibility: ContentVisibility::Published,
		));
	}
}
