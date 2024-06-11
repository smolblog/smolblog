<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;

class PictureCommandTest extends TestCase {
	public function testDeletePictureRequiresEditPermissions() {
		$command = new DeletePicture(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}

	public function testEditPictureCaptionRequiresEditPermissions() {
		$command = new EditPictureCaption(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			caption: 'Hello!',
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}

	public function testEditPictureMediaRequiresEditPermissions() {
		$command = new EditPictureMedia(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			mediaIds: [$this->randomId()],
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}
}
