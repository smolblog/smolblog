<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Test\TestCase;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\ContentExtensionServiceTestKit;
use Smolblog\Test\Kits\EventComparisonTestKit;

final class SyndicationServiceTest extends TestCase {
	use EventComparisonTestKit;
	use ContentExtensionServiceTestKit;

	private MessageBus $bus;

	protected function setUp(): void
	{
		$this->bus = $this->createMock(MessageBus::class);
		$this->subject = new SyndicationService(bus: $this->bus);
	}

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
		$bus->method('fetch')->willReturn(new class() { public $visibility = ContentVisibility::Draft; });
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
		$bus->method('fetch')->willReturn(new class() { public $visibility = ContentVisibility::Draft; });
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
