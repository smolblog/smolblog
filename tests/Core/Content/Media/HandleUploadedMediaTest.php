<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Test\TestCase;

final class HandleUploadedMediaTest extends TestCase {
	public function testUserMustHaveAuthorRightsToDispatch() {
		$command = new HandleUploadedMedia(
			file: $this->createStub(UploadedFileInterface::class),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$query = new UserHasPermissionForSite(
			siteId: $command->siteId,
			userId: $command->userId,
			mustBeAuthor: true,
		);

		$this->assertEquals($query, $command->getAuthorizationQuery());
	}
}
