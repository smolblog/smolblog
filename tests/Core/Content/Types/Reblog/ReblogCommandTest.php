<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;

class ReblogCommandTest extends TestCase {
	public function testDeleteReblogRequiresEditPermissions() {
		$command = new DeleteReblog(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			reblogId: $this->randomId(),
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}

	public function testEditReblogCommentRequiresEditPermissions() {
		$command = new EditReblogComment(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			reblogId: $this->randomId(),
			comment: 'Hello!',
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}

	public function testEditReblogUrlRequiresEditPermissions() {
		$command = new EditReblogUrl(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			reblogId: $this->randomId(),
			url: '//eph.me/music',
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}
}
