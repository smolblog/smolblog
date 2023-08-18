<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class CreatePictureTest extends TestCase {
	public function testItRequiresAuthorPermissions() {
		$command = new CreatePicture(
			mediaIds: [$this->randomId()],
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->assertEquals(
			new UserHasPermissionForSite(
				siteId: $command->siteId,
				userId: $command->userId,
				mustBeAdmin: false,
				mustBeAuthor: true
			),
			$command->getAuthorizationQuery()
		);
	}

	public function testItIsCreatedWithADefaultContentId() {
		$command = new CreatePicture(
			mediaIds: [$this->randomId()],
			caption: 'A thing.',
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertInstanceOf(Identifier::class, $command->contentId);
	}

	public function testItCanBeGivenAContentId() {
		$id = $this->randomId();
		$command = new CreatePicture(
			mediaIds: [$this->randomId()],
			caption: 'A thing.',
			contentId: $id,
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals($id, $command->contentId);
	}
}
