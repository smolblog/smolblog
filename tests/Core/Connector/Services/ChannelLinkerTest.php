<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Commands\LinkChannelToSite;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Events\ChannelSiteLinkSet;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

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
			channelId: $channel->id,
			siteId: $this->randomId(),
			userId: $this->randomId(),
			canPull: true, canPush: false,
		);
		$expectedEvent = new ChannelSiteLinkSet(
			channelId: $channel->id,
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
		$this->expectException(InvalidCommandParametersException::class);

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
