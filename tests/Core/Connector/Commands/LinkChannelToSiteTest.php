<?php

namespace Smolblog\Core\Connector\Commands;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Queries\UserCanLinkChannelAndSite;
use Smolblog\Framework\Objects\Identifier;

final class LinkChannelToSiteTest extends TestCase {
	public function testItIsAuthorizedByAUserCanLinkChannelAndSiteQuery() {
		$command = new LinkChannelToSite(
			channelId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			canPull: true, canPush: false,
		);
		$authQuery = $command->getAuthorizationQuery();

		$this->assertInstanceOf(UserCanLinkChannelAndSite::class, $authQuery);
		$this->assertEquals($command->channelId, $authQuery->channelId);
		$this->assertEquals($command->siteId, $authQuery->siteId);
		$this->assertEquals($command->userId, $authQuery->userId);
	}
}
