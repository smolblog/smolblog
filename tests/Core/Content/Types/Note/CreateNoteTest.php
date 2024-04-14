<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;

final class CreateNoteTest extends TestCase {
	public function testUserMustBeAnAuthor() {
		$command = new CreateNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, world!',
			publish: false,
		);
		$expected = new UserHasPermissionForSite(
			siteId: $command->siteId,
			userId: $command->userId,
			mustBeAdmin: false,
			mustBeAuthor: true,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}

	public function testItIsCreatedWithADefaultContentId() {
		$command = new CreateNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, world!',
			publish: false,
		);

		$this->assertInstanceOf(Identifier::class, $command->contentId);
	}

	public function testItCanBeGivenAContentId() {
		$id = $this->randomId();
		$command = new CreateNote(
			contentId: $id,
			siteId: $this->randomId(),
			userId: $this->randomId(),
			text: 'Hello, world!',
			publish: false,
		);

		$this->assertEquals($id, $command->contentId);
	}
}
