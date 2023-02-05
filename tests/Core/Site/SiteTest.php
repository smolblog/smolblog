<?php

namespace Smolblog\Core\Site;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class SiteTest extends TestCase {
	public function testItCanBeInstantiated() {
		$this->assertInstanceOf(
			Site::class,
			new Site(
				id: Identifier::createRandom(),
				handle: 'snek',
				displayName: 'The Snek',
				baseUrl: 'https://snek.smol.blog',
			)
		);
	}
}
