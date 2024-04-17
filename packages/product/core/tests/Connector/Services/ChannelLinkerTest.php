<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Commands\LinkChannelToSite;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Events\ChannelSiteLinkSet;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Test\Kits\EventComparisonTestKit;

final class ChannelLinkerTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheLinkChannelToSiteCommand() {
		$channel = new Channel(
			connectionId: $this->randomId(),
			channelKey: 'snek.smol.blog',
			displayName: 'snek.smol.blog',
			details: ['smol' => 'blog'],
		);
		$command = new LinkChannelToSite(
			channelId: $channel->getId(),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			canPull: true, canPush: false,
		);
		$expectedEvent = new ChannelSiteLinkSet(
			channelId: $channel->getId(),
			siteId: $command->siteId,
			canPull: $command->canPull,
			canPush: $command->canPush,
			connectionId: $channel->connectionId,
			userId: $command->userId,
		);

		$bus = $this->createMock(MessageBus::class);
		$bus->method('fetch')->willReturn($channel);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new ChannelLinker(bus: $bus);
		$service->onLinkChannelToSite($command);
	}

	public function testItThrowsAnExceptionWhenChannelDoesNotExist() {
		$this->expectException(EntityNotFound::class);

		$bus = $this->createStub(MessageBus::class);
		$command = new LinkChannelToSite(
			channelId: $this->randomId(),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			canPull: true, canPush: false,
		);

		$service = new ChannelLinker(bus: $bus);
		$service->onLinkChannelToSite($command);
	}
}
