<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\Commands\ChangeContentVisibility;
use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Events\ContentVisibilityChanged;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class ContentServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheEditContentBaseAttributesCommand() {
		$command = new EditContentBaseAttributes(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
			permalink: '/thing/slug-23',
			publishTimestamp: new DateTimeImmutable(),
			authorId: Identifier::createRandom(),
		);
		$expectedEvent = new ContentBaseAttributeEdited(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			permalink: '/thing/slug-23',
			publishTimestamp: $command->publishTimestamp,
			authorId: $command->authorId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new ContentService(bus: $messageBus);
		$service->onEditContentBaseAttributes($command);
	}

	public function testItHandlesTheChangeContentVisibilityCommand() {
		$command = new ChangeContentVisibility(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
			visibility: ContentVisibility::Protected,
		);
		$expectedEvent = new ContentVisibilityChanged(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			visibility: ContentVisibility::Protected,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new ContentService(bus: $messageBus);
		$service->onChangeContentVisibility($command);
	}
}
