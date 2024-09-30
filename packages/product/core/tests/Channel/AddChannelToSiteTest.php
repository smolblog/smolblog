<?php

namespace Smolblog\Core\Channel\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Events\ChannelAddedToSite;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Test\ChannelTestBase;

final class AddChannelToSiteTest extends ChannelTestBase {
	public function testHappyPath() {
		$channelId = $this->randomId();
		$siteId = $this->randomId();
		$userId = $this->randomId();

		$this->channels->expects($this->once())->method('userCanLinkChannelAndSite')->
			with(userId: $userId, channelId: $channelId, siteId: $siteId)->
			willReturn(true);

		$command = new AddChannelToSite(
			channelId: $channelId,
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectEvent(new ChannelAddedToSite(
			aggregateId: $siteId,
			entityId: $channelId,
			userId: $userId,
		));

		$this->app->execute($command);
	}

	public function testUnauthorized() {
		$channelId = $this->randomId();
		$siteId = $this->randomId();
		$userId = $this->randomId();

		$this->channels->expects($this->once())->method('userCanLinkChannelAndSite')->
			with(userId: $userId, channelId: $channelId, siteId: $siteId)->
			willReturn(false);

		$command = new AddChannelToSite(
			channelId: $channelId,
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}
}
