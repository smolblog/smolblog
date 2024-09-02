<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Queries\UserCanLinkChannelAndSite;
use Smolblog\Foundation\Value\Fields\Identifier;

final class LinkChannelToSiteTest extends TestCase {
	public function testItIsAuthorizedByAUserCanLinkChannelAndSiteQuery() {
		$command = new LinkChannelToSite(
			channelId: $this->randomId(),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			canPull: true, canPush: false,
		);
		$authQuery = $command->getAuthorizationQuery();

		$this->assertInstanceOf(UserCanLinkChannelAndSite::class, $authQuery);
		$this->assertEquals($command->channelId, $authQuery->channelId);
		$this->assertEquals($command->siteId, $authQuery->siteId);
		$this->assertEquals($command->userId, $authQuery->userId);
	}
}
