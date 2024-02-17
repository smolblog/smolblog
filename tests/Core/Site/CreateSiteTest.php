<?php

namespace Smolblog\Core\Site;

use InvalidArgumentException;
use Smolblog\Test\TestCase;

final class CreateSiteTest extends TestCase {
	public function testItCanBeInstantiated() {
		$this->assertInstanceOf(
			CreateSite::class,
			new CreateSite(
				userId: $this->randomId(),
				handle: 'something',
				displayName: 'Something.com',
				baseUrl: 'https://something.com/',
			)
		);
	}

	public function testItThrowsExceptionIfBaseUrlIsNotValid() {
		$this->expectException(InvalidArgumentException::class);

		new CreateSite(
			userId: $this->randomId(),
			handle: 'something',
			displayName: 'Something.com',
			baseUrl: '-$omething^',
		);
	}

	public function testItAuthorizesWithTheGivenUserId() {
		$command = new CreateSite(
			userId: $this->randomId(),
			handle: 'something',
			displayName: 'Something.com',
			baseUrl: 'https://something.com/',
		);

		$this->assertEquals($command->userId, $command->getAuthorizationQuery()->userId);
	}

	public function testItAuthorizesWithTheCommandUserIfItIsGiven() {
		$command = new CreateSite(
			userId: $this->randomId(),
			handle: 'something',
			displayName: 'Something.com',
			baseUrl: 'https://something.com/',
			commandUser: $this->randomId()
		);

		$this->assertEquals($command->commandUser, $command->getAuthorizationQuery()->userId);
		$this->assertNotEquals($command->userId, $command->getAuthorizationQuery()->userId);
	}
}
