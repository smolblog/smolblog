<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class TagServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheSetTagsCommand() {
		$command = new SetTags(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
			tags: ['one', 'two'],
		);
		$expectedEvent = new TagsSet(
			tagText: $command->tags,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new TagService(bus: $bus);
		$service->onSetTags($command);
	}
}
