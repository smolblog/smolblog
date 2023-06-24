<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\Federation\Commands\ProcessFollowRequest;
use Smolblog\Test\TestCase;

final class MessageTest extends TestCase {
	public function testGetFollowersForSite() {
		$query = new GetFollowersForSite($this->randomId());
		$this->assertInstanceOf(GetFollowersForSite::class, $query);

		$query->setResults([$this->createStub(Follower::class)]);
		$this->assertIsArray($query->results());
		$this->assertInstanceOf(Follower::class, $query->results()[0]);
	}

	public function testSiteByResourceUri() {
		$this->assertInstanceOf(
			SiteByResourceUri::class,
			new SiteByResourceUri('acct:snek@smol.blog'),
		);
	}
}
