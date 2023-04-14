<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class SyndicationServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheAddSyndicationLinkCommand() {
		$command = new AddSyndicationLink(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
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
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
			channels: [
				Identifier::createRandom(),
				Identifier::createRandom(),
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
