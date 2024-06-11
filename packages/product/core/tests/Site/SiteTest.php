<?php

namespace Smolblog\Core\Site;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class SiteTest extends TestCase {
	public function testItCanBeInstantiated() {
		$this->assertInstanceOf(
			Site::class,
			new Site(
				id: $this->randomId(),
				handle: 'snek',
				displayName: 'The Snek',
				baseUrl: 'https://snek.smol.blog',
				description: 'You know who I am.',
				publicKey: '---PUBLIC KEY GOES HERE---',
			)
		);
	}
}
