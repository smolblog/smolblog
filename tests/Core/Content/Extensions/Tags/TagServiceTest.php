<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class TagServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheSetTagsCommand() {
		$command = new SetTags(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			tags: ['one', 'two'],
		);
		$expectedEvent = new TagsSet(
			tagText: $command->tags,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$bus = $this->createMock(MessageBus::class);
		$bus->method('fetch')->willReturn(new class() { public $visibility = ContentVisibility::Draft; });
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new TagService(bus: $bus);
		$service->onSetTags($command);
	}
}
