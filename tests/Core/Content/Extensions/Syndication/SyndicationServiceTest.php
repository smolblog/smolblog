<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class SyndicationServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheAddSyndicationLinkCommand() {
		$command = new AddSyndicationLink(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			url: '//smol.blog/post/123',
		);
		$expectedEvent = new ContentSyndicated(
			url: '//smol.blog/post/123',
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new SyndicationService(bus: $bus);
		$service->onAddSyndicationLink($command);
	}

	public function testItHandlesTheSetSyndicationChannelsCommand() {
		$command = new SetSyndicationChannels(
			userId: $this->randomId(),
			siteId: $this->randomId(),
			contentId: $this->randomId(),
			channels: [
				$this->randomId(),
				$this->randomId(),
			],
		);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new SyndicationChannelsSet(
				channels: $command->channels,
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
			)
		));

		$service = new SyndicationService(bus: $bus);
		$service->onSetSyndicationChannels($command);
	}
}
