<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Fields\Identifier;

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

		$this->assertEquals($expcted, $actual->getId());
	}
}
