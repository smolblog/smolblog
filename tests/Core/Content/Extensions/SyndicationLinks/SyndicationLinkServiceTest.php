<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class SyndicationLinkServiceTest extends TestCase {
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
}
