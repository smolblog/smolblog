<?php

namespace Smolblog\Core\Connector\Entities;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ChannelSiteLinkTest extends TestCase {
	public function testAnIdIsKnowableFromChannelAndSite() {
		$channelId = Identifier::createRandom();
		$siteId = Identifier::createRandom();
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
