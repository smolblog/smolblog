<?php

namespace Smolblog\Core\Federation;

use DateTimeImmutable;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Test\Kits\SiteEventTestKit;
use Smolblog\Test\TestCase;

final class FollowerRemovedTest extends TestCase {
	use SiteEventTestKit;

	protected function setUp(): void {
		$this->subject = new FollowerRemoved(
			aggregateId: $this->randomId(true),
			userId: $this->randomId(true),
			provider: 'mastofed',
			providerKey: 'smolsnek',
			id: $this->randomId(true),
			timestamp: new DateTimeField('2022-02-22 22:22:22.222'),
		);
	}
}
