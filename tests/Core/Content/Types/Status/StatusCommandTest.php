<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;

final class StatusCommandTest extends TestCase {
	public function testCreateStatusIsAuthorizedByQuery() {
		$command = new CreateStatus(
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

	public function testEditStatusIsAuthorizedByQuery() {
		$command = new EditStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
			text: "What's happening?",
		);
		$expected = new UserCanEditContent(
			siteId: $command->siteId,
			userId: $command->userId,
			contentId: $command->statusId,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}

	public function testDeleteStatusIsAuthorizedByQuery() {
		$command = new DeleteStatus(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			statusId: $this->randomId(),
		);
		$expected = new UserCanEditContent(
			siteId: $command->siteId,
			userId: $command->userId,
			contentId: $command->statusId,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}
}
