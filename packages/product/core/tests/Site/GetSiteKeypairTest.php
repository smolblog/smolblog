<?php

namespace Smolblog\Core\Site;

use Smolblog\Core\User\User;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class GetSiteKeypairTest extends TestCase {
	protected function setUp(): void {
		$this->subject = new GetSiteKeypair(
			siteId: $this->randomId(true),
			userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID),
		);
	}

	public function testItIsSecuredByAQueryThatReturnsTrueIfTheUserIsSmolbot() {
		$secQuery = $this->subject->getAuthorizationQuery();

		$this->assertTrue($secQuery->results());
		$this->assertTrue($secQuery->isPropagationStopped());
	}

	public function testItIsSecuredByAQueryThatReturnsFalseIfTheUserIsNotSmolbot() {
		$secQuery = (new GetSiteKeypair(siteId: $this->randomId(), userId: $this->randomId()))->getAuthorizationQuery();

		$this->assertFalse($secQuery->results());
		$this->assertTrue($secQuery->isPropagationStopped());
	}
}
