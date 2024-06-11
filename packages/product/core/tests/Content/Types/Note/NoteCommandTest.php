<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;

final class NoteCommandTest extends TestCase {
	public function testEditNoteIsAuthorizedByQuery() {
		$command = new EditNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			text: "What's happening?",
		);
		$expected = new UserCanEditContent(
			siteId: $command->siteId,
			userId: $command->userId,
			contentId: $command->contentId,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}

	public function testDeleteNoteIsAuthorizedByQuery() {
		$command = new DeleteNote(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);
		$expected = new UserCanEditContent(
			siteId: $command->siteId,
			userId: $command->userId,
			contentId: $command->contentId,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}
}
