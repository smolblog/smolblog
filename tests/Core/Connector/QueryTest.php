<?php

namespace Smolblog\Core\Connector\Queries;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class QueryTest extends TestCase {
	public function testOtherQueriesCanBeCreated() {
		$this->assertInstanceOf(
			ChannelsForSite::class,
			new ChannelsForSite(siteId: Identifier::createRandom(), canPull: true)
		);

		$this->assertInstanceOf(
			ConnectionsForUser::class,
			new ConnectionsForUser(userId: Identifier::createRandom()),
		);

		$this->assertInstanceOf(
			SiteHasPermissionForChannel::class,
			new SiteHasPermissionForChannel(siteId: Identifier::createRandom(), channelId: Identifier::createRandom())
		);
	}
}
