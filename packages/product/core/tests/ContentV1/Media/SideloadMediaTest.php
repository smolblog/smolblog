<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Test\TestCase;

final class SideloadMediaTest extends TestCase {
	public function testUserMustHaveAuthorRightsToDispatch() {
		$command = new SideloadMedia(
			url: '//cdn.bookface.social/uploads/thing.gif',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'A thing.'
		);

		$query = new UserHasPermissionForSite(
			siteId: $command->siteId,
			userId: $command->userId,
			mustBeAuthor: true,
		);

		$this->assertEquals($query, $command->getAuthorizationQuery());
	}
}
