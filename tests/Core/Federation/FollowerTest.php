<?php

namespace Smolblog\Core\Federation;

use Smolblog\Test\TestCase;

class FollowerTest extends TestCase {
	public function testIdCanBeKnownFromProviderSlugAndKey() {
		$follower = new Follower(
			siteId: $this->randomId(),
			provider: 'test-provider',
			providerKey: 'someRandoBirdThing',
			displayName: 'Rando @person@site.tld',
			data: ['inbox' => 'https://smol.blog/'],
		);
		$expected = Follower::buildId(provider: 'test-provider', providerKey: 'someRandoBirdThing');

		$this->assertEquals($expected, $follower->id);
	}
}
