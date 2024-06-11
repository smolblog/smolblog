<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ChannelSiteLinkTest extends TestCase {
	public function testAnIdIsKnowableFromChannelAndSite() {
		$channelId = $this->randomId();
		$siteId = $this->randomId();
		$expcted = ChannelSiteLink::buildId(channelId: $channelId, siteId: $siteId);
		$actual = new ChannelSiteLink(
			channelId: $channelId,
			siteId: $siteId,
			canPull: true,
			canPush: false,
		);

		$this->assertEquals($expcted, $actual->id);
	}
}
