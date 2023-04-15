<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class QueryTest extends TestCase {
	public function testOtherQueriesCanBeCreated() {
		$this->assertInstanceOf(
			ChannelsForSite::class,
			new ChannelsForSite(siteId: $this->randomId(), canPull: true)
		);

		$this->assertInstanceOf(
			ConnectionsForUser::class,
			new ConnectionsForUser(userId: $this->randomId()),
		);

		$this->assertInstanceOf(
			SiteHasPermissionForChannel::class,
			new SiteHasPermissionForChannel(siteId: $this->randomId(), channelId: $this->randomId())
		);
	}
}
