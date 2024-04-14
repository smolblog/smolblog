<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;

final class CreateReblogTest extends TestCase {
	public function testItRequiresAuthorPermissions() {
		$command = new CreateReblog(
			url: '//smol.blog/',
			userId: $this->randomId(),
			siteId: $this->randomId(),
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

	public function testItIsCreatedWithADefaultContentId() {
		$command = new CreateReblog(
			url: 'https://echoing.green/',
			siteId: $this->randomId(),
			userId: $this->randomId(),
			comment: 'Hello, world!',
			publish: false,
		);

		$this->assertInstanceOf(Identifier::class, $command->contentId);
	}

	public function testItCanBeGivenAContentId() {
		$id = $this->randomId();
		$command = new CreateReblog(
			url: 'https://echoing.green/',
			contentId: $id,
			siteId: $this->randomId(),
			userId: $this->randomId(),
			comment: 'Hello, world!',
			publish: false,
		);

		$this->assertEquals($id, $command->contentId);
	}
}
