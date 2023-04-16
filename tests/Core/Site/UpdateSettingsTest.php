<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Test\TestCase;

final class UpdateSettingsTest extends TestCase {
	public function testUserMustBeAdminToChangeSettings() {
		$command = new UpdateSettings(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			siteName: 'Test Site',
			siteTagline: 'Because reasons.',
		);
		$auth = new UserHasPermissionForSite(siteId: $command->siteId, userId: $command->userId, mustBeAdmin: true);

		$this->assertEquals($auth, $command->getAuthorizationQuery());
	}

	public function testAtLeastOneUpdatedAttributeMustBeProvided() {
		$this->expectException(InvalidCommandParametersException::class);

		new UpdateSettings(siteId: $this->randomId(), userId: $this->randomId());
	}
}
