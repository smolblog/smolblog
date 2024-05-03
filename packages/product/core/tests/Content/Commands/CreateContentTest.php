<?php

namespace Smolblog\Core\Content\Commands;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Test\Kits\ContentTestKit;
use Smolblog\Test\TestCase;

#[CoversClass(CreateContent::class)]
final class CreateContentTest extends TestCase {
	use ContentTestKit;
	public function testItRequiresAuthorPermissionForTheSite() {
		$command = new CreateContent(
			userId: $this->randomId(),
			content: $this->sampleContent(),
		);

		$expected = new UserHasPermissionForSite(
			siteId: $command->content->siteId,
			userId: $command->userId,
			mustBeAuthor: true,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}
}
