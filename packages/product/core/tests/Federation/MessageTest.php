<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\Federation\Commands\ProcessFollowRequest;
use Smolblog\Test\TestCase;

final class MessageTest extends TestCase {
	public function testGetFollowersForSiteByProvider() {
		$query = new GetFollowersForSiteByProvider($this->randomId());
		$this->assertInstanceOf(GetFollowersForSiteByProvider::class, $query);

		$query->setResults(['abc' => [new Follower(
			siteId: $this->randomId(),
			provider: 'abc',
			providerKey: 'xyz',
			displayName: '123',
			details: [],
		)]]);
		$this->assertIsArray($query->results());
		$this->assertInstanceOf(Follower::class, $query->results()['abc'][0]);
	}

	public function testSiteByResourceUri() {
		$this->assertInstanceOf(
			SiteByResourceUri::class,
			new SiteByResourceUri('acct:snek@smol.blog'),
		);
	}
}
