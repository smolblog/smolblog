<?php

namespace Smolblog\Core\Content\Types\Reblog;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;

class ReblogCommandTest extends TestCase {
	public function testCreateReblogRequiresAuthorPermissions() {
		$command = new CreateReblog(
			url: '//smol.blog/',
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
			publish: false,
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

	public function testDeleteReblogRequiresEditPermissions() {
		$command = new DeleteReblog(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			reblogId: Identifier::createRandom(),
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}

	public function testEditReblogCommentRequiresEditPermissions() {
		$command = new EditReblogComment(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			reblogId: Identifier::createRandom(),
			comment: 'Hello!',
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}

	public function testEditReblogUrlRequiresEditPermissions() {
		$command = new EditReblogUrl(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			reblogId: Identifier::createRandom(),
			url: '//eph.me/music',
		);

		$this->assertInstanceOf(UserCanEditContent::class, $command->getAuthorizationQuery());
	}
}
