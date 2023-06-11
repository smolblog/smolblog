<?php

namespace Smolblog\Core\User;

use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Test\TestCase;

final class UpdateProfileTest extends TestCase {
	public function testAtLeastOneUpdatedAttributeMustBeProvided() {
		$this->expectException(InvalidCommandParametersException::class);

		new UpdateProfile(userId: $this->randomId(), profileId: $this->randomId());
	}

	public function testTheUserMustBeAuthorized() {
		$command = new UpdateProfile(
			userId: $this->randomId(),
			profileId: $this->randomId(),
			handle: 'someone',
			displayName: 'Someone',
			pronouns: 'they/them',
		);

		$this->assertInstanceOf(UserCanEditProfile::class, $command->getAuthorizationQuery());
	}
}
