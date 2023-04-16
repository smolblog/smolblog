<?php

namespace Smolblog\Core\User;

use Smolblog\Test\TestCase;

final class QueryTest extends TestCase {
	public function testUserByIdWillInstantiate() {
		$this->assertInstanceOf(UserById::class, new UserById($this->randomId()));
	}

	public function testUserSitesWillInstantiate() {
		$this->assertInstanceOf(UserSites::class, new UserSites($this->randomId()));
	}
}
