<?php

namespace Smolblog\Core\Federation;

use Smolblog\Test\TestCase;

class FollowerTest extends TestCase {
	public function testIdCanBeKnownFromProviderSlugAndKey() {
		$siteId = $this->randomId();
		$follower = new Follower(
			siteId: $siteId,
			provider: 'test-provider',
			providerKey: 'someRandoBirdThing',
			displayName: 'Rando @person@site.tld',
			details: ['inbox' => 'https://smol.blog/'],
		);
		$expected = Follower::buildId(siteId: $siteId, provider: 'test-provider', providerKey: 'someRandoBirdThing');

		$this->assertEquals($expected, $follower->id);
	}
}
