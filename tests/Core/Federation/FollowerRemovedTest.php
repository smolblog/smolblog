<?php

namespace Smolblog\Core\Federation;

use DateTimeImmutable;
use Smolblog\Test\SiteEventTestKit;
use Smolblog\Test\TestCase;

final class FollowerRemovedTest extends TestCase {
	use SiteEventTestKit;

	protected function setUp(): void {
		$this->subject = new FollowerRemoved(
			siteId: $this->randomId(true),
			userId: $this->randomId(true),
			provider: 'mastofed',
			providerKey: 'smolsnek',
			id: $this->randomId(true),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22.222'),
		);
	}
}
